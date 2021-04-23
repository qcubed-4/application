<?php
use QCubed\Action\ActionParams;
use QCubed\Control\FileControl;
use QCubed\Control\Image;
use QCubed\Folder;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;
use QCubed\QString;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    /** @var  Image */
    protected $lblImage;
    /** @var  Button */
    protected $btnUpload;
    /** @var  FileControl */
    protected $flcImage;

    protected function formCreate()
    {
        $this->lblImage = new Image($this);
        if (isset($_SESSION['file_control'])) {
            $this->lblImage->ImageUrl = "../images/files/" . $_SESSION['file_control']; // This keeps things working on a page reload.
        }
        $this->lblImage->AlternateText = "File Control Picture";

        $this->btnUpload = new Button($this);
        $this->btnUpload->Text = "Upload";
        $this->btnUpload->OnClick(new \QCubed\Action\Server("btnUpload_Click")); // MUST be a Server action, and not an Ajax action!

        $this->flcImage = new \QCubed\Control\FileControl($this);
        $this->flcImage->Required = true;
    }

    protected function btnUpload_Click(ActionParams $params)
    {
        $file = $this->flcImage->File;

        $imageDir = dirname(__DIR__ ) . "/images/files";

        Folder::makeDirectory($imageDir, 0700);

        // Our strategy here is just for managing the demo on a shared server. We allow the directory to fill with 100
        // files at most, and then clear it out.
        if (Folder::countItems($imageDir) > 100) {
            Folder::emptyContents($imageDir);
        }

        // So that the file is only visible by the current user. Prevents someone from uploading inappropriate content
        // that is visible to others.
        $filename = QString::getRandomString(10) . ".jpg";
        move_uploaded_file($file, $imageDir . "/" . $filename);
        $_SESSION["file_control"] = $filename;

        $this->lblImage->ImageUrl = "../images/files/" . $_SESSION['file_control'];
    }

    protected function formValidate() {
        $blnValid = parent::formValidate();

        if (is_array($this->flcImage->File)) {
            $this->flcImage->Warning = "Select only a single file.";
            $blnValid = false;
        }
        // Just checking for mime type is not enough for complete security, but its a good first start for users trying to
        // do the right thing.
        elseif ($this->flcImage->Type != "image/jpeg" &&
            $this->flcImage->Type != "image/jpg") {

            $this->flcImage->Warning = "File must be a JPEG file.";
            $blnValid = false;
        }

        // Here you might use exif to further check for a jpeg file, or use the PHP image functions to attempt to copy the image.
        return $blnValid;
    }

}

ExampleForm::run('ExampleForm');

