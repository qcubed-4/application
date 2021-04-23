<?php require('../includes/header.inc.php'); ?>
<?php $this->RenderBegin(); ?>

<div id="instructions">
	<h1>The FileControl Control</h1>

	<p>The <strong>FileControl</strong> control presents a Select File button that allows the user to select a file to upload to the
        server. The control itself is fairly simple, offering a basic "file" type of textbox that all browsers support.
    However, the bigger subject of how to support file uploads can be quite complicated.</p>

    <p>PHP itself has some built-in support for file uploads, using the $_FILES super global to provide the names of files
    uploaded when a POST call is made to the browser. The FileControl control uses this built-in support to get the file
    the user selected. Since this only works with POST calls, to initiate the actual upload, you <strong>must</strong> use a
    <strong>Server</strong> action. <strong>Ajax</strong> actions will not work.</p>

    <p> Your <strong>Server</strong> action should do something with the file immediately. PHP will automatically delete
        the uploaded file after the server call is processed. PHP provides some functions to manage this process.
        See PHP's <a href="http://php.net/manual/en/features.file-upload.php">Handling File Uploads</a>
        article for more information.</p>

    <p>Any time you allow a user to upload a file to your server, you are creating a security risk. You must be sure
    you understand the risks and how to mitigate them. PHP will take care of uploading the file itself to a temporary
    directory, and you can find out where the file is with the <strong>->File</strong> attribute of the <strong>FileControl</strong>.
    After that, it is up to you to check the file to make sure it is the type of file you expect, and then either move the file
    out of the temporary directory, or process it how you would like. You could put the file into a  BLOB in your database, move
    it to another directory, upload it to a cloud service, or whatever. Just be sure that you take precautions to prevent a
    malicious attack unexpectedly coming from a file upload. </p>

    <p>This particular example is a simple example of handling upload of a single jpeg file, but is not complete. It does not
    handle multiple files selected, nor does it provide enough checks to ensure that the file uploaded is actually a jpeg file,
    but it should get you started.</p>

</div>

<div id="demoZone">
	<p><?php $this->flcImage->RenderWithError(); ?></p>
    <p><?php $this->lblImage->Render(); ?></p>
	<p><?php $this->btnUpload->Render(); ?></p>
</div>

<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>