<?php
namespace api\service;

class Code{
    const OK = 0;
    const MethodError = 20000;
    const Exist = 20004;
    const ArgumentError = 30000;
    const SysError = 40000;
    const NotFound = 40004;
    const NotAuth = 40005;
    const TooBad = 40006;

    const ValidateSignatureError = -40001;
    const ParseXmlError = -40002;
    const ComputeSignatureError = -40003;
    const IllegalAesKey = -40004;
    const ValidateAppidError = -40005;
    const EncryptAESError = -40006;
    const DecryptAESError = -40007;
    const IllegalBuffer = -40008;
    const EncodeBase64Error = -40009;
    const DecodeBase64Error = -40010;
    const GenReturnXmlError = -40011;
}
