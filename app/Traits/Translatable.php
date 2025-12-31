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
            if (!empty($this->attributes[$enAttribute])) {
                return $this->attributes[$enAttribute];
            }
        }

        // Return default (Arabic) value
        return $this->attributes[$attribute] ?? null;
    }
}
