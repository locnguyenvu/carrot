<?php
namespace Tikivn\Exception;

use GuzzleHttp\Exception as GuzzleHttpException;
use Carrot\Exception as CarrotException;

class Factory
{
    public static function make(\Exception $e) {
        if ($e instanceof GuzzleHttpException\GuzzleException) {
            return static::httpException($e);
        }
    }

    public static function httpException (GuzzleHttpException\GuzzleException $e) {
        switch ($e->getCode()) {
            case 401:
                return new CarrotException\Http\UnauthorizedException($e);
                break;
            case 400:
                return new CarrotException\Http\BadRequestException($e);
               break;
            case 500:
                return new CarrotException\Http\InternalServerErrorException($e);
                break;
            default:
                return new CarrotException\Http\HttpException($e);
        }
    }
}