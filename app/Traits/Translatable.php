<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait Translatable
{
    /**
     * Get the translated value of an attribute.
     *
     * @param string $attribute
     * @return mixed
     */
    public function trans(string $attribute)
    {
        $locale = App::getLocale();

        if ($locale === 'en') {
            $enAttribute = $attribute . '_en';
            // Return English value if it exists and is not empty
            // Use getAttribute to ensure the value is loaded
            $enValue = $this->getAttribute($enAttribute);
            if (!empty($enValue)) {
                return $enValue;
            }
        }

        // Return default (Arabic) value
        return $this->getAttribute($attribute) ?? null;
    }

    /**
     * Accessor for trans attribute - allows checking if trans method exists
     * This prevents Laravel from calling trans() without arguments when accessed as property
     *
     * @return bool
     */
    public function getTransAttribute(): bool
    {
        return true;
    }
}
