<?php
namespace VediX;

abstract class SectionDefaultController {
	
	private $code;
	private $application;
	private $type;
	private $title;
	private $subdir;
	
	public function __construct($section) {
		$this->code = $section['code'];
		$this->application = $section['application'];
		$this->type = $section['type'];
		$this->title = $section['title'];
		$this->subdir = $section['subdir'];
	}
	
	public function formOpen() {
	
		$form = new TObject();

		$template = $this->code . '.admin';
		$renderTemplate = 'catalog/' . $this->subdir . $template;
		$filename = HD_ROOT . $renderTemplate . '.html';
		if (! is_file($filename)) {
			RETURN Request::stop(
				'Не найден шаблон раздела!'.Utils::rn.Utils::rn.
				'Веб-приложение: '.$this->application.Utils::rn.
				'Раздел: '.$this->code.Utils::rn.
				'Искомый файл: '.$filename,
				Request::HH_INTERNALERROR,
				'Не найден шаблон раздела!'
			);
		}

		$form->application = $this->application;
		$form->section = $this->code;
		$form->title = $this->title;
		global $mustache;
		$form->html = $mustache->render( '/' . $renderTemplate);
		$form->cssFile = '/' . $renderTemplate . '.css';
		$form->jsFile = '/' . $renderTemplate . '.js';

		if (! is_file( HD_ROOT . substr($form->cssFile, 1) )) unset($form->cssFile);
		if (! is_file( HD_ROOT . substr($form->jsFile, 1) )) unset($form->jsFile);

		return $form;
	}
	
	abstract public function jxSwitch();
}

class AdminModel {
	public $config;
	public $request;
	public $user;
	public $currentDate;
	public $section = '{}';
	public function __construct() {
		$this->config = new TObject();
		$this->config->main = ConfigCatalog::getSection('main');
		$this->request = Request::getArray();
		$this->user = User::getArray();
		$this->currentDate = Utils::russianDate();
		$this->section = '{}';
	}
}
?>