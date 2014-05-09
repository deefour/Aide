<?php

class TestNewsArticle extends TestArticle {

  public function validations(array $context = []) {
    $rules = [
      'title' => [ 'required', ],
    ];

    $rules['slug-has-hyphen'] = function() {
      if (strpos($this->slug, '-') === false) {
        return 'unhyphenated-slug';
      }
    };

    return $rules;
  }

}