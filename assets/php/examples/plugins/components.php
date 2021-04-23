<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions" class="full">
	<h1>Make Your Own Plugin, Part 1: Writing Custom Controls</h1>

	<p>Once you have used QCubed for a while, you may find you have a need to create your
		own custom Control, or connect QCubed to an already existing javascript widget.</p>

	<p>Start by familiarizing yourself with the various plugins currently written. Take a look
		at their source code and how the directories are structured. Also take a look at the QCubed
		core files that are implementations of <a href="http://jqueryui.com">JQueryUI</a> components, like the
        Autocomplete component.</p>

	<p>There are a many ways to write a custom widget. A couple have already been explained under
		<a href="../other_controls/sample.php">Creating Your Own Control</a> and
		<a href="../composite/intro.php">Creating a Composite Control</a>.
		These methods modify currently available QCubed controls. Studying these examples will give you a good
		understanding of how controls work on the PHP side.</p>

	<h2>Connecting Javascript Controls to QCubed</h2>

	<p>There are now many javascript libraries available on the internet that contain a huge variety of javascript controls.
		These controls can be connected to QCubed so that all the functionality of the control is available to you within PHP.
		Below is a discussion of some of the aspects of connecting a javascript control to QCubed. For the most part, you can
		follow the examples of the currently written libraries and JQueryUI components. The basic goal is to maintain
		sychronization between the javascript control's data, and the data in the PHP control.</p>
	<h3>Setup</h3>

		<p>First, you must decide on what the base HTML object of the control will be, and this will determine what the base
			of your <strong>Control</strong> wil be. For example, if your javascript widget attaches to a


		&lt;div&gt; block, you should have your control extend from the <strong>\QCubed\Control\Panel</strong> class, because the
			<strong>\QCubed\Control\Panel</strong> outputs a &lt;div&gt;. If you have additional HTML that you need to output,
			implement a <strong>getControlHtml</strong> method in your control.</p>
		<p>QCubed includes <a href="http://www.jquery.com">jQuery</a> and uses it for much of its internal function.
			However, it changes the '$' variable to '$j'. So,   to refer to your control for example, you would output
			the following javascript:</p>
	<p><code>$j('#controlId')</code></p>
		<p>where controlId is the id of your control. You can get your id by accessing the -&gt;<strong>ControlId</strong>
			parameter of your control object in PHP. </p>
		<p>Make sure you include the javascript for the widget itself by calling <strong>addJavascriptFile</strong>
			from your PHP constructor.</p>
	
	<h3>Moving Data from PHP to Javascript</h3>
        <p>How you move data from PHP to Javascript will depend a bit on your javascript widget.</p>
    <ul>
        <li>
            If your JavaScript widget uses a JavaScript function to attach itself to an html object, you should
            implement the <strong>makeJqOptions()</strong> method. This method connects parameters for your initialization
            method with Properties you make available through __get and __set methods.
        </li>
        <li>
            If instead your JavaScript widget uses html attributes to define its data, implement the <strong>getControlHtml()</strong>
            method, and add the attributes in the attribute overrides when calling <strong>renderTag()</strong>.
        </li>
        <li>
            If neither of these work for you, you can override the <strong>getEndScript</strong> method to output the javascript that will connect the javascript
                widget to the HTML you created. This is also the place where you would output any class member variables of
                your PHP in such a way that the javascript will read it. Whenever your control is completely redrawn, all the HTML and
                the javascript in <strong>getEndScript</strong> will be drawn again, updating the control to the current
                data in PHP. To cause a redraw, call the <strong>refresh()</strong> method, which it inherits from <strong>\QCubed\Control\ControlBase</strong>.
                See examples of this is done in the various Gen classes in the Jqui directory.</p>
        </li>
    </ul>

	
		<p>When you want to update your control after initially drawing it, use
			the <strong>addAttributeScript()</strong> method. This will execute a javascript function on your control that you would use to do
			the update. It is also designed so that if the entire control is redrawn, the javascript will not execute, since
			it will not be needed since the whole control is being drawn. For example, you could do this:</p>
	<p><code>$this->AddAttributeScript('val', $strValue);</code></p>
		<p>to set the <strong>value</strong> of your control to $strValue on the javascript side. Remember to also save
			that data into your PHP control, because later your control might completely redraw, and it will need to
			draw using the new value.</p>

	<h3>Moving Data from  Javascript to PHP</h3>
		<p>Data comes from the javascript via a variety of mechanisms.</p>
		<p>The primary method is via Post variables. All input items, like textboxes and checkboxes are submitted this
			way. Also select items like lists send there data this way.</p>
	    <p>To read post variables, create a subclass <strong>Control</strong> in PHP and override the <strong>parsePostData()</strong>
			method. Within that function, examine the <strong>$_POST</strong> superglobal and update your internal state accordingly.

		<p>To have your javascript send POST variables, you can do one of the following:</p>
        <ul>
          <li>Atttach your control to a standard html form element, like a textbox or checkbox and on the PHP side, make
			  your <strong>Control</strong> a subclass of the corresponding <strong>Control</strong> type. The data
			  will automatically be updated in QCubed.</li> See <strong>\QCubed\Project\Jqui\Checkbox</strong> for an example of this method.
          <li>Create a hidden input element and store the data that the javascript widget represents into the hidden
			  element. Update that hidden element in javascript whenever the data changes, but also trigger the
			  <strong>qformObjChanged</strong> event to notify QCubed that the control changed. For the hidden input(s),
			  give them an id that is the same as the parent control, followed by an underscore and whatever text you
			  need to uniquely identify it. This will associate the hidden control with the parent control. Implement the
			  <strong>parsePostData()</strong> method within your control to read the data in the post variable and put
			  it into your <strong>Control</strong>.See the <strong>\QCubed\Control\ImageInput</strong> control source for an
			  example of this method.</li>
		  <li>Whenever your control changes, call <strong>qcubed.setAdditionalPostVar(name, val)</strong>. This will add
				the name and value to the list of variables posted. <strong>val</strong> can be a string, array or object.  If you cannot detect a change for
				some reason, you can add a listener on the <strong>qposting</strong> event on the form. That event is fired
				right before any post variables are sent to PHP, which gives you a good opportunity to call
				<strong>qcubed.setAdditionalPostVar</strong>.</li>
		</ul>
        <p>Another way to push data to PHP is to use the <strong>qc.recordControlModification</strong> function whenever
			an aspect of your control changes. Call
			<strong>qc.recordControlModification</strong> from your javascript control and pass it your control id, a
			control property, and a new value for the property. The control property is a property in your PHP control
			that you can set through the <strong>__set</strong> magic method. Calling <strong>recordControlModification</strong>
			in javascript will cause QCubed to pass that value to your PHP control through that property.</p> See the
			<strong>\QCubed\Project\Jqui\Accordion</strong> control for an example of this method.
        <p>Events can also be used to read data from the control during the processing of a specific event as discussed
			in the <a href="../other_controls/js_return_param_example.php">jQuery Controls: Adding Actions</a> example.</p>
        <p>Many javascript controls these days use  ajax or javascript mechanisms to read data in real time. Connecting
			these controls to QCubed is  more complicated, and is dependent on the specific implementation. Here are
			some examples:</p>
        <ul>
          <li>Use a special NoScript ajax action to link a javascript function with a PHP <strong>\QCubed\Event\EventBase</strong>. The
			  event gets triggered when the javascript widget wants data. Within that event, execute a
			  <strong>\QCubed\Project\Application::executeJavascript()</strong> command to send the data back to the widget. This is how
			  the <strong>\QCubed\Project\Jqui\Autocomplete</strong> widget works as well as the datatables plugin.</li>
          <li>Create a php file that outputs json data that you want to send to the javascript widget. Set the name of
			  the php file in the javascript widget as being the source of its data. If you initialize QCubed within
			  that php file, you will have all of the QCubed functionality available for generating the json file.</li>
          <li>Create a php file that outputs a javascript array and include that file in your html so that whenever the
			  page is drawn, it updates the array.</li>
        </ul>
        <h2>Adding Your Control to the Code Generation Process</h2>
        <p>If your control is designed to edit a basic type that might come from the database, like an integer, varchar
			string, or even a list of items that are a result of a primary key relationship, you can add some code to
			your control to include it in the code generation process to make it easier for other users to bind your
			control to fields in the database.</p>
        <p>If you have based your control on a type that binds to a pre-existing control, you might not need to do
			anything extra. The <strong>\QCubed\Project\Jqui\SelectMenu</strong> control is an example of this, which is simply a javascript
			overlay on a list box. </p>
        <p>However, if your interface is a unique way of editing data, you will need to create a new class
			called ${LIBRARY_CONTROL_CLASS}_CodeGenerator that extends AbstractControl_CodeGenerator (or more likely Control_CodeGenerator)
			to bind the control to the database. See the <strong>Slider_CodeGenerator</strong> for an example of how to generate the binding code needed.</p>
        <p>Implement the <strong>GetModelConnectorParams</strong> function to allow a user of the control to set all of your
			<strong>__set</strong> parameters through the ModelConnector Designer user-interface. This will make it much
			easier for users to know and use the capabilities of your control.</p>
        <p>Finally, in order to make your control available to the designer as an option for the particular data types
			your control manipulates, create a <strong>control_registry.inc.php</strong> file and put it in the
            /install/project/includes/configuration/control_registry directory. Name it "your_control_name".inc.php.</p>
        <p>&nbsp;</p>
<p><a href="packaging.php">Read the next chapter</a> to
  learn about ways to package and distribute your plugin.</p>
</div>

<style>#viewSource { display: none; }</style>

<?php require('../includes/footer.inc.php'); ?>