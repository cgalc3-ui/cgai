<?php

namespace App\Http\Controllers\Api;

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
     * Get all tickets for the authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();

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
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $tickets,
        ]);
    }

    /**
     * Create a new ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max per image
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($request, $user) {
            // Create ticket
            $ticket = Ticket::create([
                'user_id' => $user->id,
                'subject' => $request->subject,
                'description' => $request->description,
                'priority' => $request->priority ?? 'medium',
                'status' => 'open',
            ]);

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

            $ticket->load(['messages.attachments', 'attachments']);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء التذكرة بنجاح',
                'data' => $ticket,
            ], 201);
        });
    }

    /**
     * Get ticket details with messages
     */
    public function show(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        // Check authorization
        if ($ticket->user_id !== $user->id && !$user->isAdminOrStaff()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول لهذه التذكرة',
            ], 403);
        }

        $ticket->load([
            'user',
            'assignedUser',
            'messages.user',
            'messages.attachments',
            'attachments',
        ]);

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }

    /**
     * Add message to ticket
     */
    public function addMessage(Request $request, $ticketId)
    {
        $request->validate([
            'message' => 'required|string',
            'is_internal' => 'nullable|boolean',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $user = $request->user();
        $ticket = Ticket::findOrFail($ticketId);

        // Check authorization
        if ($ticket->user_id !== $user->id && !$user->isAdminOrStaff()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول لهذه التذكرة',
            ], 403);
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

            $message->load(['user', 'attachments']);

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال الرسالة بنجاح',
                'data' => $message,
            ], 201);
        });
    }

    /**
     * Update ticket status (admin/staff only)
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if (!$user->isAdminOrStaff()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية لتحديث حالة التذكرة',
            ], 403);
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

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة التذكرة بنجاح',
            'data' => $ticket->fresh()->load(['assignedUser']),
        ]);
    }
}
