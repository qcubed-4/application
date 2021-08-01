	/**
	 * A button was clicked. Override to do something different than the default or process further.
	 * @param string $strFormId
	 * @param string $strControlId
	 * @param mixed $param
	 */
	public function buttonClick($strFormId, $strControlId, $param)
    {
		switch ($param) {
			case 'save':
				$this->save();
				break;

			case 'delete':
				$this->pnl<?= $strPropertyName ?>->delete();
                $this->close();
				break;

			case 'cancel':
                $this->close();
                break;
		}
	}
