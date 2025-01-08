<?php

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $frameworkConfig): void {
    $frameworkConfig->router()->defaultUri('%env(MERITE_URI)%');
};
