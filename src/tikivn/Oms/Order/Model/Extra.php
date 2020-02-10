<?php
namespace Tikivn\Oms\Order\Model;

class Extra extends \Carrot\Common\Model
{
    public function __construct(string $extraString = null) {
        if ($extraString == null) {
            return;
        }

        $extraData = json_decode(\stripslashes($extraString), true);
        if (JSON_ERROR_NONE == \json_last_error()) {
            $this->_properties = $extraData;
        }
    }
}