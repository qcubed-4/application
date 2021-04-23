<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Security: Cross-Site Scripting (XSS) Prevention</h1>
	<p>Cross-site scripting, or XSS, is a type of software vulnerability that allows attackers to inject JavaScript
		into web pages viewed by other users. This attack, if executed successfully, can provide the attacker
		a way to steal other users' cookies, thus enabling them to gain unauthorized access to your website.
		It's one of the most frequently exploited vulnerabilities on the Web; read more about
		it <a href="http://en.wikipedia.org/wiki/Cross-site_scripting">on Wikipedia</a>.</p>

	<p>QCubed comes with two layers of protection against XSS. Both of these are enabled by default, and you don't
		need to do anything to make use of them.</p>

	<p>The first layer is around filtering input - particularly in <strong>TextBox</strong> controls. This is about filtering
		the input that the user has placed into the text box, and rejecting or removing any potential script and tags from it.
		By default QCubed will use either PHP's built-in sanitizer, or HTML Purifier if its installed. However this behaviour
		can be changed per TextBox instance by setting its <strong>CrossScripting</strong>
		property to one of the following values:</p>
	<ul>
		<li><strong>TextBox::XSS_ALLOW</strong> completely disables any checks and filtering and would let any posted data through. This is the most
			insecure option and should be avoided unless you have very good reasons for it.</li>
        <li><strong>TextBox::XSS_HTML_ENTITIES</strong> simply calls PHP's htmlentities() function on the submitted text. This will protect against
            cross-site scripting attacks, however it will not filter anything out, which may still be undesirable.</li>
        <li><strong>TextBox::XSS_PHP_SANITIZE</strong> uses PHP's built-in String sanitizer. Removes tags from the input. You can further refine this
            by specifying SanitizeFilterOptions.</li>
		<li><strong>TextBox::XSS_HTML_PURIFIER</strong> is the option that offers fine-grained control over filtering. It uses the well
			known <a href="http://htmlpurifier.org/">HTML Purifier</a> library. From the library's home page:
			<blockquote>
				"HTML Purifier will not only remove all malicious
				code (better known as XSS) with a thoroughly audited,
				secure yet permissive whitelist,
				it will also make sure your documents are
				standards compliant, something only achievable with a
				comprehensive knowledge of W3C's specifications."
			</blockquote>
		</li>
	</ul>

	<p>The default value used for creating TextBox instances can be altered by setting
		<strong>$strCrossScripting</strong> in /project/qcubed/Control/TextBox.php to one of the values above.</p>

	<p>The second layer is about escaping output - so that if a piece of undesirable JavaScript somehow made it into
		the database, QCubed will run it through the HTMLEntities function, escaping each possible entity (such as
		an HTML tag, for example, &lt;script&gt; tag).</p>

	<p>Note that sometimes, there's a need to allow users to input some form of HTML (for example, if you want to
		allow the input of a few tags, such as the innocent tags &lt;b&gt,&lt;i&gt). In those cases, you need to disable
		the second protection (output filtering), and also list the tags that you want to allow by specifying:</p>

	<div style="margin-left: 20px"><code>
			$this->txtTextbox2->CrossScripting = QCrossScripting::HTMLPurifier;<br/>
			$this->txtTextbox2->SetPurifierConfig("HTML.Allowed", "b,i");
		</code></div>

	<p>See the five textboxes below to learn more about how this XSS protection works. For each box, paste the following
    into the text box to test it.
    </p>
    <code>
        Hello! &lt;script&gt;alert("I am an evil attacker.")&lt;/script&gt;&lt;b&gt;Hello&lt;/b&gt; &lt;i&gt;again&lt;/i&gt;!
    </code>
</div>

<div id="demoZone">
	<p><strong>Textbox protected with the default <code>TextBox::XSS_PHP_SANITIZE</code>. Tags are stripped.</strong></p>
	<p><?php $this->txtTextbox1->Render() ?></p>
	<p><?php $this->btnButton1->Render(); ?></p>
	<p>&nbsp;<?php $this->lblLabel1->Render() ?></p>

	<p><strong>Textbox protected with <code>TextBox::XSS_HTML_ENTITIES</code>:</strong></p>
	<p><?php $this->txtTextbox2->Render() ?></p>
	<p><?php $this->btnButton2->Render(); ?></p>
	<p>&nbsp;<?php $this->lblLabel2->Render() ?></p>

	<p><strong>Textbox protected with <code>TextBox::XSS_HTML_PURIFIER</code> with default settings:</strong></p>
	<p><?php $this->txtTextbox3->Render() ?></p>
	<p><?php $this->btnButton3->Render(); ?></p>
	<p>&nbsp;<?php $this->lblLabel3->Render() ?></p>

	<p><strong>Textbox protected with <code>TextBox::XSS_HTML_PURIFIER</code> with a set of tags that's allowed (ex.&lt;b&gt;, &lt;i&gt;). Note that you should make any change to the text in this input, in order it to be correctly processed. This is because of optimization made in the qcubed 3.0 version: HTML Purifier is designed to filter text coming from the browser, not from the PHP side.</strong></p>
	<p><?php $this->txtTextbox4->Render() ?></p>
	<p><?php $this->btnButton4->Render(); ?></p>
	<p>&nbsp;<?php $this->lblLabel4->Render() ?></p>

	<p><strong>Unprotected textbox (uses <code>TextBox::XSS_ALLOWED</code>). Not recommended - don't do this unless you have a good reason!:</strong></p>
	<p><?php $this->txtTextbox5->Render() ?></p>
	<p><?php $this->btnButton5->Render(); ?></p>
	<p>&nbsp;<?php $this->lblLabel5->Render() ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>
