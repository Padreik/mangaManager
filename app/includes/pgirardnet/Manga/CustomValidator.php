<?php

namespace pgirardnet\Manga;

class CustomValidator extends \Illuminate\Validation\Validator {
    public function validateLinkIsImage($attribute, $value, $parameters) {
        $url = filter_var($value, FILTER_VALIDATE_URL);
        if ($url) {
            try {
                return getimagesize($value) !== false;
            }
            catch (\ErrorException $e) {
                return false;
            }
        }
        else {
            return false;
        }
    }
    
    protected function replaceLinkIsImage($message, $attribute, $rule, $parameters) {
        return "Veuillez entrer un lien vers une image valide";
    }
}
