<?php
namespace VediX;

class ConfigAdmin extends SectionDefaultController {
	
	public function jxSwitch() {
		$jx = Request::JX();
		$response = null;
		switch ($jx) {
			case 'configLoad': $response = $this->configLoad(); break;
			case 'configSave': $response = $this->configSave(); break;
			default: {
				RETURN Request::stop(
					'Получена неизвестная команда!'.Utils::rn.Utils::rn.
					'Веб-приложение: '.$this->section['application'].Utils::rn.
					'Раздел: '.$this->section['code'].Utils::rn.
					'jx: '.$jx.Utils::rn,
					Request::HH_INTERNALERROR,
					'Получена неизвестная команда!'
				);
			}
		}
		return $response;
	}
	
	private function configLoad() {
	
		$data = new TObject();

		$data->body = file_get_contents(HD_ROOT . 'catalog.ini');

		return $data;
	}
	
	private function configSave() {
	
		$data = new TObject();

		$body = Utils::_REQUEST('body');

		if (! $body) {
			RETURN Request::stop(
				'Конфиг должен содержать текст!'.Utils::rn,
				Request::HH_LOGICERROR,
				'Конфиг должен содержать текст!'
			);
		}

		file_put_contents(HD_ROOT . 'catalog.ini', stripslashes($body));

		return $data;
	}
}
?>