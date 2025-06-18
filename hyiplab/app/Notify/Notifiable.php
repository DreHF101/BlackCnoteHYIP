<?php

namespace Hyiplab\Notify;

interface Notifiable
{
	public function send();

	public function prevConfiguration();
}