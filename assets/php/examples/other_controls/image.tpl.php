<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>
	
	<div id="instructions">
		<h1>Images and Image Buttons</h1>

        <p>This example shows you how to create a number of kinds of dynamic images and image buttons that rely on
        basic HTML and CSS.</p>
		
		<p>The <strong>Image</strong> control allows you to create a dynamically assigned image and place it on the screen.</p>

        <p>The <strong>ImageInput</strong> is similar, but also responds to actions. When you click on the image, it will
            record the location clicked, and you can query the <strong>ClickX</strong> and <strong>ClickY</strong> properties
            to get the coordinate clicked.</p>

        <p>Since an HTML button can have any kind of internal markup, you can create a <strong>Button</strong> with an
            internal Image object for a slightly different effect. Or, you can create a button with a background image,
            but in that case, you will need to specify the size of the button.
         </p>

        <p>
            There are other possibilities as well that may be available as additional QCubed plugins or 3rd party controls.
            Dynamically created images which use the GD library or ImageMagick may be available.
        </p>
    </div>

<div id="demoZone">
    <p>Image: <?php $this->lblImage->render(); ?></p>
    <p>ImageInput (click on this): <?php $this->btnImageInput->render(); ?></p>
    <p>Button with internal Image: <?php $this->btnImage->render(); ?></p>
    <p>Button with background image: <?php $this->btnBgImage->render(); ?></p>
    <p>Image with client-side map (click the right eye): <?php $this->btnImageMap->render(); ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>