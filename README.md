# Project từ sự lười biếng

## Cài đặt

```
$ composer install
$ chmod +x ./carrot
```

Tạo file `.env`
```
OMS_DOMAIN="http://oms.tiki.domain"
```

## Hướng dẫn sử dụng

### Token
#### Set token để sử dụng api service
Command: `./carrot settoken`

### Refund
#### Tạo cho đơn hàng bị huỷ
Command: `./carrot refund/create_for_canceled_order`
