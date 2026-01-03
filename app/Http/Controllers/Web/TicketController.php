<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of tickets
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Ticket::where('user_id', $user->id)
            ->with(['assignedUser', 'latestMessage']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Store a newly created ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $user = auth()->user();

        return DB::transaction(function () use ($request, $user) {
            $locale = app()->getLocale();
            
            // Create ticket with translation support
            $ticketData = [
                'user_id' => $user->id,
                'priority' => $request->priority ?? 'medium',
                'status' => 'open',
            ];
            
            // Save based on current locale
            if ($locale === 'en') {
                $ticketData['subject_en'] = $request->subject;
                $ticketData['description_en'] = $request->description;
                // Keep Arabic fields empty or copy if needed
                $ticketData['subject'] = $request->subject; // Fallback
                $ticketData['description'] = $request->description; // Fallback
            } else {
                // Arabic (default)
                $ticketData['subject'] = $request->subject;
                $ticketData['description'] = $request->description;
            }
            
            $ticket = Ticket::create($ticketData);

            // Create initial message
            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => $request->description,
                'is_internal' => false,
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('tickets/' . $ticket->id, 'public');
                    
                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'message_id' => $message->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Notify admins about new ticket
            if (class_exists(\App\Services\NotificationService::class)) {
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->notifyAdmins(
                    'new_ticket',
                    'messages.new_support_ticket',
                    'messages.new_support_ticket_from_user',
                    [
                        'ticket_id' => $ticket->id,
                        'user_id' => $user->id,
                        'user' => $user->name,
                        'subject' => $ticket->subject,
                    ]
                );
            }

            // Return JSON for AJAX requests
            if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء التذكرة بنجاح',
                    'data' => $ticket->load('messages', 'attachments'),
                ], 201);
            }

            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'تم إنشاء التذكرة بنجاح');
        });
    }

    /**
     * Display the specified ticket
     */
    public function show(Ticket $ticket)
    {
        $user = auth()->user();

        // Check authorization
        if ($ticket->user_id !== $user->id && !$user->isAdminOrStaff()) {
            return redirect()->route('tickets.index')
                ->with('error', 'ليس لديك صلاحية للوصول لهذه التذكرة');
        }

        $ticket->load([
            'user',
            'assignedUser',
            'messages.user',
            'messages.attachments',
            'attachments',
        ]);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Add message to ticket
     */
    public function addMessage(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
            'is_internal' => 'nullable|boolean',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $user = auth()->user();

        // Check authorization
        if ($ticket->user_id !== $user->id && !$user->isAdminOrStaff()) {
            return back()->with('error', 'ليس لديك صلاحية للوصول لهذه التذكرة');
        }

        // Only admin/staff can create internal messages
        $isInternal = $request->boolean('is_internal') && $user->isAdminOrStaff();

        return DB::transaction(function () use ($request, $user, $ticket, $isInternal) {
            // Create message
            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => $request->message,
                'is_internal' => $isInternal,
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('tickets/' . $ticket->id, 'public');
                    
                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'message_id' => $message->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Update ticket status if it was closed/resolved
            if (in_array($ticket->status, ['resolved', 'closed']) && !$isInternal) {
                $ticket->update(['status' => 'open', 'resolved_at' => null]);
            }

            return back()->with('success', 'تم إرسال الرسالة بنجاح');
        });
    }

    /**
     * Update ticket status (admin/staff only)
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $user = auth()->user();

        if (!$user->isAdminOrStaff()) {
            return back()->with('error', 'ليس لديك صلاحية لتحديث حالة التذكرة');
        }

        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket->update([
            'status' => $request->status,
            'assigned_to' => $request->assigned_to ?? $ticket->assigned_to,
            'resolved_at' => $request->status === 'resolved' ? now() : null,
        ]);

        return back()->with('success', 'تم تحديث حالة التذكرة بنجاح');
    }
}
