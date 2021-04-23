<?php
/**
 * These are arrays of changes that were manually edited to help with automatically converting version 3 code to version 4.
 */

$a['const']['QFontFamily::Arial'] = '\\QCubed\\Html::FONT_FAMILY_ARIAL';
$a['const']['QFontFamily::Helvetica'] = '\\QCubed\\Html::FONT_FAMILY_HELVETICA';
$a['const']['QFontFamily::Tahoma'] = '\\QCubed\\Html::FONT_FAMILY_TAHOMA';
$a['const']['QFontFamily::TrebuchetMs'] = '\\QCubed\\Html::FONT_FAMILY_TREBUCHET_MS';
$a['const']['QFontFamily::Verdana'] = '\\QCubed\\Html::FONT_FAMILY_VERDANA';
$a['const']['QFontFamily::TimesNewRoman'] = '\\QCubed\\Html::FONT_FAMILY_TIMES_NEW_ROMAN';
$a['const']['QFontFamily::Georgia'] = '\\QCubed\\Html::FONT_FAMILY_GEORGIA';
$a['const']['QFontFamily::LucidaConsole'] = '\\QCubed\\Html::FONT_FAMILY_LUCIDA_CONSOLE';
$a['const']['QFontFamily::CourierNew'] = '\\QCubed\\Html::FONT_FAMILY_LUCIDA_COURIER_NEW';
$a['const']['QFontFamily::Courier'] = '\\QCubed\\Html::FONT_FAMILY_LUCIDA_COURIER';

$a['const']['QCausesValidation::None'] = '\\QCubed\\Control\\ControlBase::CAUSES_VALIDATION_NONE';
$a['const']['QCausesValidation::AllControls'] = '\\QCubed\\Control\\ControlBase::CAUSES_VALIDATION_ALL';
$a['const']['QCausesValidation::SiblingsAndChildren'] = '\\QCubed\\Control\\ControlBase::CAUSES_VALIDATION_SIBLINGS_AND_CHILDREN';
$a['const']['QCausesValidation::SiblingsOnly'] = '\\QCubed\\Control\\ControlBase::CAUSES_VALIDATION_SIBLINGS_ONLY';

$a['const']['QTextAlignType::Left'] = '\\QCubed\\Html::TEXT_ALIGN_LEFT';
$a['const']['QTextAlignType::Right'] = '\\QCubed\\Html::TEXT_ALIGN_RIGHT';

$a['const']['QTextMode::SingeLine'] = '\\QCubed\\Control\\TextBoxBase::SINGLE_LINE';
$a['const']['QTextMode::MultiLine'] = '\\QCubed\\Control\\TextBoxBase::MULTI_LINE';
$a['const']['QTextMode::Password'] = '\\QCubed\\Control\\TextBoxBase::PASSWORD';
$a['const']['QTextMode::Search'] = '\\QCubed\\Control\\TextBoxBase::SEARCH';
$a['const']['QTextMode::Number'] = '\\QCubed\\Control\\TextBoxBase::NUMBER';
$a['const']['QTextMode::Email'] = '\\QCubed\\Control\\TextBoxBase::EMAIL';
$a['const']['QTextMode::Tel'] = '\\QCubed\\Control\\TextBoxBase::TEL';
$a['const']['QTextMode::Url'] = '\\QCubed\\Control\\TextBoxBase::URL';

$a['const']['QRepeatDirection::Horizontal'] = '\\QCubed\\Control\\ListControl::REPEAT_HORIZONTAL';
$a['const']['QRepeatDirection::Vertical'] = '\\QCubed\\Control\\ListControl::REPEAT_VERTICAL';

$a['const']['QSelectionMode::Single'] = '\\QCubed\\Control\\ListBoxBase::SELECTION_MODE_SINGLE';
$a['const']['QSelectionMode::Multiple'] = '\\QCubed\\Control\\ListBoxBase::SELECTION_MODE_MULTIPLE';
$a['const']['QSelectionMode::None'] = '\\QCubed\\Control\\ListBoxBase::SELECTION_MODE_NONE';

$a['const']['QJsPriority::Standard'] = '\\QCubed\\ApplicationBase::PRIORITY_STANDARD';
$a['const']['QJsPriority::High'] = '\\QCubed\\ApplicationBase::PRIORITY_HIGH';
$a['const']['QJsPriority::Low'] = '\\QCubed\\ApplicationBase::PRIORITY_LOW';
$a['const']['QJsPriority::Exclusive'] = '\\QCubed\\ApplicationBase::PRIORITY_EXCLUSIVE';
$a['const']['QJsPriority::Last'] = '\\QCubed\\ApplicationBase::PRIORITY_LAST';

$a['func']['QApplication::Translate'] = 't';
$a['func']['QApplication::PathInfo'] = '\\QCubed\\Project\\Application::instance()->context()->pathInfo';
$a['func']['QApplication::QueryString'] = '\\QCubed\\Project\\Application::instance()->context()->queryStringItem';
$a['func']['QApplication::IsBrowser'] = '\\QCubed\\Project\\Application::instance()->context()->isBrowser';
$a['func']['QApplication::HtmlEntities'] = '\\QCubed\\QString::htmlEntities';
$a['func']['QApplication::MakeDirectory'] = '\\QCubed\\QFolder::makeDirectory';
$a['func']['QApplication::SetErrorHandler'] = '$objHandler = new \\QCubed\\Error\\Handler';
$a['func']['QApplication::RestoreErrorHandler'] = '$objHandler->restore';
$a['func']['QApplication::GenerateQueryString'] = '\\QCubed\\QString::generateQueryString';
$a['func']['QApplication::DisplayAlert'] = '\\QCubed\\Project\\Application::displayAlert';
$a['func']['QApplication::ExecuteJsFunction'] = '\\QCubed\\Project\\Application::executeJsFunction';
$a['func']['QApplication::ExecuteSelectorFunction'] = '\\QCubed\\Project\\Application::executeSelectorFunction';
$a['func']['QApplication::ExecuteControlCommand'] = '\\QCubed\\Project\\Application::executeControlCommand';
$a['func']['QApplication::ExecuteJavaScript'] = '\\QCubed\\Project\\Application::executeJavaScript';

$a['func']['QCrossScripting::Allow'] = '\\QCubed\\Control\\TextBoxBase::XSS_ALLOW';
$a['func']['QCrossScripting::HtmlEntities'] = '\\QCubed\\Control\\TextBoxBase::XSS_HTML_ENTITIES';
$a['func']['QCrossScripting::HTMLPurifier'] = '\\QCubed\\Control\\TextBoxBase::XSS_HTML_PURIFIER';

$a['warn']['QCrossScripting::Deny'] = 'QCrossScripting::Deny has been removed. Use XSS_HTML_PURIFIER instead.';
$a['warn']['QCrossScripting::Legacy'] = 'QCrossScripting::Legacy has been removed. Use XSS_HTML_PURIFIER instead.';

$a['func']['QApplication::isIpInRange'] = '\\QCubed\\AuthBase::isIpInRange';
$a['func']['QApplication::isRemoteAdminSession'] = '\\QCubed\\AuthBase::isRemoteAdminSession';
$a['func']['QApplication::checkRemoteAdminSession'] = '\\QCubed\\AuthBase::checkRemoteAdminSession';


$a['regex']['QApplication::\\$EncodingType'] = '\\QCubed\\Project\\Application::encodingType()';
$a['regex']['QApplication::\\$CliMode'] = '\\QCubed\\Project\\Application::instance()->context()->cliMode()';
$a['regex']['QApplication::\\$ServerAddress'] = '\\QCubed\\Project\\Application::instance()->context()->serverAddress()';
$a['regex']['QApplication::\\$ScriptFilename'] = '\\QCubed\\Project\\Application::instance()->context()->scriptFileName()';
$a['regex']['QApplication::\\$ScriptName'] = '\\QCubed\\Project\\Application::instance()->context()->scriptName()';
$a['regex']['QApplication::\\$PathInfo'] = '\\QCubed\\Project\\Application::instance()->context()->pathInfo()';
$a['regex']['QApplication::\\$QueryString'] = '\\QCubed\\Project\\Application::instance()->context()->queryString()';
$a['regex']['QApplication::\\$RequestUri'] = '\\QCubed\\Project\\Application::instance()->context()->requestUri()';
$a['regex']['QApplication::\\$BrowserType'] = '\\QCubed\\Project\\Application::instance()->context()->browserType()';
$a['regex']['QApplication::\\$BrowserVersion'] = '\\QCubed\\Project\\Application::instance()->context()->browserVersion()';
$a['regex']['QApplication::\\$DocumentRoot'] = '\\QCubed\\Project\\Application::instance()->context()->docRoot()';
$a['regex']['QApplication::\\$Minimize'] = '\\QCubed\\Project\\Application::instance()->context()->minimize()';
$a['regex']['QApplication::\\$RequestMode'] = '\\QCubed\\Project\\Application::instance()->context()->requestMode()';

$a['regex']['QApplicationBase::\\$EncodingType'] = '\\QCubed\\Project\\Application::encodingType()';
$a['regex']['QApplicationBase::\\$CliMode'] = '\\QCubed\\Project\\Application::instance()->context()->cliMode()';
$a['regex']['QApplicationBase::\\$ServerAddress'] = '\\QCubed\\Project\\Application::instance()->context()->serverAddress()';
$a['regex']['QApplicationBase::\\$ScriptFilename'] = '\\QCubed\\Project\\Application::instance()->context()->scriptFileName()';
$a['regex']['QApplicationBase::\\$ScriptName'] = '\\QCubed\\Project\\Application::instance()->context()->scriptName()';
$a['regex']['QApplicationBase::\\$PathInfo'] = '\\QCubed\\Project\\Application::instance()->context()->pathInfo()';
$a['regex']['QApplicationBase::\\$QueryString'] = '\\QCubed\\Project\\Application::instance()->context()->queryString()';
$a['regex']['QApplicationBase::\\$RequestUri'] = '\\QCubed\\Project\\Application::instance()->context()->requestUri()';
$a['regex']['QApplicationBase::\\$BrowserType'] = '\\QCubed\\Project\\Application::instance()->context()->browserType()';
$a['regex']['QApplicationBase::\\$BrowserVersion'] = '\\QCubed\\Project\\Application::instance()->context()->browserVersion()';
$a['regex']['QApplicationBase::\\$DocumentRoot'] = '\\QCubed\\Project\\Application::instance()->context()->docRoot()';
$a['regex']['QApplicationBase::\\$Minimize'] = '\\QCubed\\Project\\Application::instance()->context()->minimize()';
$a['regex']['QApplicationBase::\\$RequestMode'] = '\\QCubed\\Project\\Application::instance()->context()->requestMode()';

$a['regex']['QConvertNotation'] = '\\QCubed\\QString';

$a['warn']['QCallType'] = 'QCallType has been removed. Use Application::isAjax, or Application::instance()->context()->requestMode() instead.';

$a['const']['QFormGen::Both'] = '\\QCubed\\ModelConnector\\Options::FORMGEN_BOTH';
$a['const']['QFormGen::LabelOnly'] = '\\QCubed\\ModelConnector\\Options::FORMGEN_LABEL_ONLY';
$a['const']['QFormGen::ControlOnly'] = '\\QCubed\\ModelConnector\\Options::FORMGEN_CONTROL_ONLY';
$a['const']['QFormGen::None'] = '\\QCubed\\ModelConnector\\Options::FORMGEN_NONE';

$a['const']['QModelConnectorCreateType::CreateOrEdit'] = '\\QCubed\\ModelConnector\\Options::CREATE_OR_EDIT';
$a['const']['QModelConnectorCreateType::CreateOnRecordNotFound'] = '\\QCubed\\ModelConnector\\Options::CREATE_ON_RECORD_NOT_FOUND';
$a['const']['QModelConnectorCreateType::EditOnly'] = '\\QCubed\\ModelConnector\\Options::EDIT_ONLY';

$a['regex']['\\bForm_Run\\s{0,3}\\('] = 'formRun(';
$a['regex']['\\bForm_Load\\s{0,3}\\('] = 'formLoad(';
$a['regex']['\\bForm_Create\\s{0,3}\\('] = 'formCreate(';
$a['regex']['\\bForm_PreRender\\s{0,3}\\('] = 'formPreRender(';
$a['regex']['\\bForm_Initialize\\s{0,3}\\('] = 'formInitialize(';
$a['regex']['\\bForm_Validate\\s{0,3}\\('] = 'formValidate(';
$a['regex']['\\bForm_Invalid\\s{0,3}\\('] = 'formInvalid(';
$a['regex']['\\bForm_Exit\\s{0,3}\\('] = 'formExit(';

$a['regex']['\\$this->RenderBegin\\('] = '$this->renderBegin(';
$a['regex']['\\$this->RenderEnd\\('] = '$this->renderEnd(';

$a['regex']['->HorizontalAlign'] = '->TextAlign';
$a['class']['QHorizontalAlign'] = '\\QCubed\Css\\TextAlign';
$a['const']['\\QCubed\Css\\TextAlignType::Center'] = '\\QCubed\Css\\TextAlignType::CENTER';
$a['const']['\\QCubed\Css\\TextAlignType::Right'] = '\\QCubed\Css\\TextAlignType::RIGHT';
$a['const']['\\QCubed\Css\\TextAlignType::Left'] = '\\QCubed\Css\\TextAlignType::LEFT';

$a['regex']['const EventName'] = 'const EVENT_NAME';
$a['regex']['const JsReturnParam'] = 'const JS_RETURN_PARAM';

$a['const']['QDateTimePickerType::Date'] = '\\QCubed\Control\\DateTimePicker::SHOW_DATE';
$a['const']['QDateTimePickerType::DateTime'] = '\\QCubed\Control\\DateTimePicker::SHOW_DATE_TIME';
$a['const']['QDateTimePickerType::DateTimeSeconds'] = '\\QCubed\Control\\DateTimePicker::SHOW_DATE_TIME_SECONDS';
$a['const']['QDateTimePickerType::Time'] = '\\QCubed\Control\\DateTimePicker::SHOW_TIME';
$a['const']['QDateTimePickerType::TimeSeconds'] = '\\QCubed\Control\\DateTimePicker::SHOW_TIME_SECONDS';

$a['class']['QCacheProviderLocalMemory'] = '\\QCubed\\Cache\\LocalMemoryCache';

return $a;