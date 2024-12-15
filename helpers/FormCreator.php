<?php


namespace Helpers;

use App\helpers\utils\security;
use Core\Record;
use Enums\Alignment;
use Enums\DisplayType;

class FormCreator
{
    private $record;
    private $fieldsDefault = [];
    private $fields = [];
    private $html = '';
    private $requestUrl = '';
    public function __construct(Record $record, $uri)
    {
        $this->record = $record;
        $this->fieldsDefault = $record->getField()->fields->fields;
        $this->requestUrl = $uri;
        self::start();
    }


    public function start()
    {
        foreach ($this->fieldsDefault as $field) {
            if ($field->isEditableInput) {
                $this->fields[$field->focusOrder][$field->name] = $field;
            }
        }
        self::generateFields();
    }

    public function generateFields()
    {
        $size = count($this->fields);
        for ($i = 1; $i < $size; $i++) {
            foreach ($this->fields[$i] as $key => $value) {
                $this->html .= '<div class="form-group d-flex flex-column flex-grow-1">';
                if ($value->displayType == DisplayType::TEXT) {
                    $this->html .= $this->TEXT($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::NUMBER) {
                    $this->html .= $this->NUMBER($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::DATE) {
                    $this->html .= $this->DATE($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::TIME) {
                    $this->html .= $this->TIME($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::DATETIME) {
                    $this->html .= $this->DATETIME($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::CHECKBOX) {
                    $this->html .= $this->CHECKBOX($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::RADIO) {
                    $this->html .= $this->RADIO($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::COMBOBOX) {
                    $this->html .= $this->COMBOBOX($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::TEXTAREA) {
                    $this->html .= $this->TEXTAREA($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::COLORPICKER) {
                    $this->html .= $this->COLORPICKER($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::PASSWORD) {
                    $this->html .= $this->PASSWORD($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::FILE) {
                    $this->html .= $this->FILE($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::HIDDEN) {
                    $this->html .= $this->HIDDEN($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::RANGE) {
                    $this->html .= $this->RANGE($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::EMAIL) {
                    $this->html .= $this->EMAIL($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::LABEL) {
                    $this->html .= $this->LABEL($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                } else if ($value->displayType == DisplayType::MULTISELECT) {
                    $this->html .= $this->MULTISELECT($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
                }
                $this->html .= '</div>';
            }
        }

        foreach ($this->fields[0] as $key => $value) {
            $this->html .= '<div class="form-group d-flex flex-column flex-grow-1">';
            if ($value->displayType == DisplayType::TEXT) {
                $this->html .= $this->TEXT($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::NUMBER) {
                $this->html .= $this->NUMBER($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::DATE) {
                $this->html .= $this->DATE($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::TIME) {
                $this->html .= $this->TIME($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::DATETIME) {
                $this->html .= $this->DATETIME($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::CHECKBOX) {
                $this->html .= $this->CHECKBOX($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::RADIO) {
                $this->html .= $this->RADIO($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::COMBOBOX) {
                $this->html .= $this->COMBOBOX($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::TEXTAREA) {
                $this->html .= $this->TEXTAREA($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::COLORPICKER) {
                $this->html .= $this->COLORPICKER($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::PASSWORD) {
                $this->html .= $this->PASSWORD($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::FILE) {
                $this->html .= $this->FILE($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::HIDDEN) {
                $this->html .= $this->HIDDEN($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::RANGE) {
                $this->html .= $this->RANGE($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::EMAIL) {
                $this->html .= $this->EMAIL($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::LABEL) {
                $this->html .= $this->LABEL($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            } else if ($value->displayType == DisplayType::MULTISELECT) {
                $this->html .= $this->MULTISELECT($value, $value->maxLength, $value->minLength, $value->placeholder, $value->defaultValue, $value->cls . ' form-control', $value->style, $value->mask, $value->readonly, $value->disabled, $value->hidden, $value->isRequired, $value->tooltip, $value->align);
            }
            $this->html .= '</div>';
        }
        $this->html .= $this->HIDDEN("csrf_token", 0, 255, "", security::getCSRF(), "", "", "", true, false, true, true, "", "");
        $this->html .= $this->HIDDEN($this->record->getField()->primaryKey, 0, 255, "", "", "", "", "", true, false, true, false, "", "");
    }


    public function TEXT($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }
        // Şimdilik gerekli değil
        $width = $value->width;
        $height = $value->height;
        if ($align == Alignment::LEFT) {
            $cls .= ' text-start';
        } else if ($align == Alignment::RIGHT) {
            $cls .= ' text-end';
        } else if ($align == Alignment::CENTER) {
            $cls .= ' text-center';
        }
        $htm .= '<input type="text" name="' . $value->name . '" id="' . $value->name . '" class="' . $cls . '" style="' . $style . '" value="' . $defaultValue . '" placeholder="' . $placeholder . '" maxlength="' . $maxLength . '" minlength="' . $minLength . '" data-mask="' . $mask . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '" />';

        return $htm;
    }

    public function NUMBER($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }

        $maxValue = $value->maxValue;
        $minValue = $value->minValue;
        if ($align == Alignment::LEFT) {
            $cls .= ' text-start';
        } else if ($align == Alignment::RIGHT) {
            $cls .= ' text-end';
        } else if ($align == Alignment::CENTER) {
            $cls .= ' text-center';
        }

        $htm .= '<input type="number" name="' . $value->name . '" id="' . $value->name . '" class="' . $cls . '" style="' . $style . '" value="' . $defaultValue . '" placeholder="' . $placeholder . '" max="' . $maxValue . '" min="' . $minValue . '" data-mask="' . $mask . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '" />';

        return $htm;
    }

    public function DATE($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }

        $maxValue = $value->maxValue;
        $minValue = $value->minValue;
        if ($align == Alignment::LEFT) {
            $cls .= ' text-start';
        } else if ($align == Alignment::RIGHT) {
            $cls .= ' text-end';
        } else if ($align == Alignment::CENTER) {
            $cls .= ' text-center';
        }

        $htm .= '<input type="time" name="' . $value->name . '" id="' . $value->name . '" class="' . $cls . '" style="' . $style . '" value="' . $defaultValue . '" placeholder="' . $placeholder . '" max="' . $maxValue . '" min="' . $minValue . '" data-mask="' . $mask . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '" />';

        return $htm;
    }

    public function TIME($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }

        $maxValue = $value->maxValue;
        $minValue = $value->minValue;
        if ($align == Alignment::LEFT) {
            $cls .= ' text-start';
        } else if ($align == Alignment::RIGHT) {
            $cls .= ' text-end';
        } else if ($align == Alignment::CENTER) {
            $cls .= ' text-center';
        }

        $htm .= '<input type="datetime-local" name="' . $value->name . '" id="' . $value->name . '" class="' . $cls . '" style="' . $style . '" value="' . $defaultValue . '" placeholder="' . $placeholder . '" max="' . $maxValue . '" min="' . $minValue . '" data-mask="' . $mask . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '" />';

        return $htm;
    }

    public function DATETIME($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }

        $maxValue = $value->maxValue;
        $minValue = $value->minValue;
        if ($align == Alignment::LEFT) {
            $cls .= ' text-start';
        } else if ($align == Alignment::RIGHT) {
            $cls .= ' text-end';
        } else if ($align == Alignment::CENTER) {
            $cls .= ' text-center';
        }

        $htm .= '<input type="datetime-local" name="' . $value->name . '" id="' . $value->name . '" class="' . $cls . '" style="' . $style . '" value="' . $defaultValue . '" placeholder="' . $placeholder . '" max="' . $maxValue . '" min="' . $minValue . '" data-mask="' . $mask . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '" />';

        return $htm;
    }

    public function CHECKBOX($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        return $htm;
    }

    public function RADIO($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        return $htm;
    }

    public function COMBOBOX($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }

        $htm .= '<select name="' . $value->name . '" id="' . $value->name . '" class="' . $cls . '" style="' . $style . '" placeholder="' . $placeholder . '" value="' . $defaultValue . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '">';
        if ($value->comboAdapter instanceof SimpleComboAdapter) {
            $pairs = $value->comboAdapter->getPairs();
            foreach ($pairs as $pair) {
                $htm .= '<option value="' . $pair[0] . '" ' . ($defaultValue == $pair[0] ? 'selected' : '') . '>' . $pair[1] . '</option>';
            }
        }
        $htm .= '</select>';

        return $htm;
    }

    public function TEXTAREA($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }

        $maxValue = $value->maxValue;
        $minValue = $value->minValue;
        if ($align == Alignment::LEFT) {
            $cls .= ' text-start';
        } else if ($align == Alignment::RIGHT) {
            $cls .= ' text-end';
        } else if ($align == Alignment::CENTER) {
            $cls .= ' text-center';
        }

        $htm .= '<textarea name="' . $value->name . '" id="' . $value->name . '" class="' . $cls . '" style="' . $style . '" placeholder="' . $placeholder . '" maxlength="' . $maxLength . '" minlength="' . $minLength . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '">' . $defaultValue . '</textarea>';

        return $htm;
    }

    public function COLORPICKER($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }

        $maxValue = $value->maxValue;
        $minValue = $value->minValue;
        if ($align == Alignment::LEFT) {
            $cls .= ' text-start';
        } else if ($align == Alignment::RIGHT) {
            $cls .= ' text-end';
        } else if ($align == Alignment::CENTER) {
            $cls .= ' text-center';
        }

        $htm .= '<input type="color" name="' . $value->name . '" id="' . $value->name . '" class="' . $cls . '" style="' . $style . '" placeholder="' . $placeholder . '" maxlength="' . $maxLength . '" minlength="' . $minLength . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '" value="' . $defaultValue . '">';
        return $htm;
    }

    public function PASSWORD($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }

        $maxValue = $value->maxValue;
        $minValue = $value->minValue;
        if ($align == Alignment::LEFT) {
            $cls .= ' text-start';
        } else if ($align == Alignment::RIGHT) {
            $cls .= ' text-end';
        } else if ($align == Alignment::CENTER) {
            $cls .= ' text-center';
        }

        $htm .= '<input type="password" name="' . $value->name . '" id="' . $value->name . '" class="' . $cls . '" style="' . $style . '" placeholder="' . $placeholder . '" maxlength="' . $maxLength . '" minlength="' . $minLength . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '" value="' . $defaultValue . '">';
        return $htm;
    }
    public function FILE($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        return $htm;
    }
    public function HIDDEN($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        $htm .= '<input type="hidden" name="' . $value . '" id="' . $value . '" class="' . $cls . '" style="' . $style . '" placeholder="' . $placeholder . '" maxlength="' . $maxLength . '" minlength="' . $minLength . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '" value="' . $defaultValue . '">';
        return $htm;
    }
    public function RANGE($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }

        $maxValue = $value->maxValue;
        $minValue = $value->minValue;
        if ($align == Alignment::LEFT) {
            $cls .= ' text-start';
        } else if ($align == Alignment::RIGHT) {
            $cls .= ' text-end';
        } else if ($align == Alignment::CENTER) {
            $cls .= ' text-center';
        }


        $htm .= '<input type="range" name="' . $value->name . '" id="' . $value->name . '" class="' . $cls . '" style="' . $style . '" placeholder="' . $placeholder . '" maxlength="' . $maxLength . '" minlength="' . $minLength . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '" value="' . $defaultValue . '">';
        return $htm;
    }
    public function EMAIL($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }

        $maxValue = $value->maxValue;
        $minValue = $value->minValue;
        if ($align == Alignment::LEFT) {
            $cls .= ' text-start';
        } else if ($align == Alignment::RIGHT) {
            $cls .= ' text-end';
        } else if ($align == Alignment::CENTER) {
            $cls .= ' text-center';
        }

        $htm .= '<input type="email" name="' . $value->name . '" id="' . $value->name . '" class="' . $cls . '" style="' . $style . '" placeholder="' . $placeholder . '" maxlength="' . $maxLength . '" minlength="' . $minLength . '" ' . ($readonly == true ? 'readonly' : '') . ' ' . ($disabled == true ? 'disabled' : '') . ' ' . ($hidden == true ? 'hidden' : '') . ' ' . ($required == true ? 'required' : '') . ' title="' . $tooltip . '" value="' . $defaultValue . '">';
        return $htm;
    }

    public function LABEL($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }
        return $htm;
    }

    public function MULTISELECT($value, $maxLength, $minLength, $placeholder, $defaultValue, $cls, $style, $mask, $readonly, $disabled, $hidden, $required, $tooltip, $align): string
    {
        $htm = "";
        if ($value->label != null && !empty($value->label)) {
            $htm .= '<label for="' . $value->name . '" class="' . ($value->isRequired == true ? 'required' : '') . '">' . $value->label . '</label>';
        }

        $maxValue = $value->maxValue;
        $minValue = $value->minValue;
        if ($align == Alignment::LEFT) {
            $cls .= ' text-start';
        } else if ($align == Alignment::RIGHT) {
            $cls .= ' text-end';
        } else if ($align == Alignment::CENTER) {
            $cls .= ' text-center';
        }




        return $htm;
    }

    public function render()
    {

        echo '<form id="' . $this->record->getField()->tableName . '" class="d-flex flex-row gap-3 gap-lg-10 flex-wrap">';
        echo $this->html;
        echo '</form>';

    }


    public function renderGetJS()
    {
        $getUrl = SITE_URL . '/' . $this->requestUrl . 'get/' . $this->record->getField()->tableName.'/';
        $htm = '<script>';
        $htm .= 'function getItem(selected) { clearRequiredForm($("#'.$this->record->getField()->tableName.'"));';
        $htm .= 'if (selected.length !== 1) { showAlert("Lütfen düzenlemek için bir kayıt seçiniz", "warning");  return; } showLoading(true);';
        $htm .= '$.post("'.$getUrl.'" + selected[0].'.$this->record->getField()->primaryKey.').done(function(response) { showLoading(false); console.log(response);';
        $htm .= 'if (response.status === "success") { resetForm($("#'.$this->record->getField()->tableName.'_modal")); $("#'.$this->record->getField()->tableName.'_modal").modal("show"); fillFormBulk($("#'.$this->record->getField()->tableName.'"), response.data)  } showAlert(response.message, response.status); }); showLoading(false); }</script>';
        echo $htm;
    }


    public function renderSaveJS(){
        $saveUrl = SITE_URL . '/' . $this->requestUrl . 'save/' . $this->record->getField()->tableName.'/';
        $htm = '<script>';
        $htm .= 'function saveItem() { let req =  requiredForm($("#'.$this->record->getField()->tableName.'")); if (req === false) { return; } else { showLoading(true);';
        $htm .= '$.post("'.$saveUrl.'", $("#'.$this->record->getField()->tableName.'").serialize()).done(function(response) { showLoading(false); console.log(response);';
        $htm .= 'if (response.status === "success") { $("#'.$this->record->getField()->tableName.'_modal").modal("hide");  table_'.$this->record->getField()->tableName.'_table.ajax.reload(); } showAlert(response.message, response.status); }); showLoading(false); } }';
        $htm .= '</script>';
        echo $htm;
    }
}
