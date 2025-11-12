<?php
use QCubed\Action\ActionParams;
use QCubed\Action\Ajax;
use QCubed\Action\Alert;
use QCubed\Action\RegisterClickPosition;
use QCubed\Control\Image;
use QCubed\Control\ImageArea;
use QCubed\Control\ImageInput;
use QCubed\Project\Application;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    protected Image $lblImage;
    protected ImageInput $btnImageInput;
    protected Button $btnImage;
    protected Image $lblImage2;
    protected Button $btnBgImage;
    protected Image $btnImageMap;

    protected function formCreate(): void
    {
        $this->lblImage = new Image($this);
        $this->lblImage->ImageUrl = "../images/emoticons/1.png";
        $this->lblImage->AlternateText = "Emoticon";

        $this->btnImageInput = new ImageInput($this);
        $this->btnImageInput->AlternateText = "Click Me";
        $this->btnImageInput->ImageUrl = "../images/emoticons/2.png";
        $this->btnImageInput->onClick (
            [
                new RegisterClickPosition(),    // make sure we first register the click position so our click handler can see it.
                new Ajax("btnImage_Click")
            ]
        );

        $this->btnImage = new Button($this);
        $this->btnImage->AutoRenderChildren = true;
        $this->lblImage2 = new Image($this->btnImage);
        $this->lblImage2->ImageUrl = "../images/emoticons/3.png";

        $this->btnBgImage = new Button($this);
        $this->btnBgImage->BackgroundImageUrl = "../images/emoticons/4.png";
        $this->btnBgImage->Width = 200;
        $this->btnBgImage->Height = 200;

        $this->btnImageMap = new Image($this);
        $this->btnImageMap->ImageUrl = "../images/emoticons/5.png";

        $area = new ImageArea($this->btnImageMap);
        $area->Shape = ImageArea::SHAPE_CIRCLE;
        $area->Coordinates = [80, 55, 20];
        $area->setHtmlAttribute("href", "#"); // Makes the pointer show it clickable
        $area->onClick(new Alert("Eyeball"));
    }

    protected function btnImage_Click(ActionParams $params): void
    {
        /** @var ImageInput $btn */
        $btn = $params->Control;
        Application::displayAlert("Click at " . $btn->ClickX . "," . $btn->ClickY);
    }
}

ExampleForm::run('ExampleForm');

