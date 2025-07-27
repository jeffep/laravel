<?php

public function handle() {
    $this->call('sensor:migrate-data');
    $this->call('user:migrate-data');
    $this->call('shelly:migrate-data');
}

