<?php

namespace jhuta\phpmvc\Form;

use jhuta\phpmvc\Model;

class TextareaField extends BaseField {
  // public const TYPE_TEXT = 'text';
  // public const TYPE_PASSWORD = 'password';
  // public const TYPE_NUMBER = 'number';

  public string $type;

  public function __construct(Model $model, $attribute) {
    parent::__construct($model, $attribute);
    // $this->type = self::TYPE_TEXT;
  }

  public function __toString() {
    return sprintf(
      '
    <div class="form-group">
      <label for="">%s</label>
      %s
      <div class="invalid-feedback">
        %s
      </div>
    </div>
',
      $this->model->getLabel($this->attribute),
      $this->renderInput(),
      $this->model->getFirstError($this->attribute)
    );
  }

  // public function passwordField() {
  //   $this->type = self::TYPE_PASSWORD;
  //   return $this;
  // }

  public function renderInput(): string {
    return sprintf(
      '<textarea name="%s" class="form-control %s">%s</textarea>',
      $this->attribute,
      $this->model->hasError($this->attribute) ? 'is-invalid' : '',
      $this->model->{$this->attribute}
    );
  }
}