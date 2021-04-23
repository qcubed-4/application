<?php
use QCubed\Action\ActionParams;
use QCubed\Action\Ajax;
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
    protected $lblImage;
    protected $btnImageInput;
    protected $btnImage;
    protected $txtMessage2;
    protected $lblImage2;
    protected $btnBgImage;
    protected $btnImageMap;

    protected function formCreate()
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
        $area->setHtmlAttribute("href", "#"); // Makes the pointer show its clickable
        $area->onClick(new \QCubed\Action\Alert("Eyeball"));
    }

    protected function btnImage_Click(ActionParams $params)
    {
        /** @var ImageInput $btn */
        $btn = $params->Control;
        Application::displayAlert("Clicked at " . $btn->ClickX . "," . $btn->ClickY);
    }

    protected function lstFont_Change($strFormId, $strControlId, $strParameter)
    {
        // Set the lblMessage's font to the new font file
        $this->lblMessage->FontNames = $this->lstFont->SelectedValue;
    }
}

ExampleForm::run('ExampleForm');

