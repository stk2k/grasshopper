<?php
namespace Grasshopper\curl;

use Grasshopper\Error;

class CurlError implements Error
{
    const CATEGORY_CURL = 'curl';
    const CATEGORY_CURL_MULTI = 'curl_m';
    const CATEGORY_CURL_SHARE = 'curl_sh';

    /* CURLcode */
    const E_OK = 0;
    const E_UNSUPPORTED_PROTOCOL = 1;
    const E_FAILED_INIT = 2;
    const E_URL_MALFORMAT = 3;
    const E_NOT_BUILT_IN = 4;
    const E_COULDNT_RESOLVE_PROXY = 5;
    const E_COULDNT_RESOLVE_HOST = 6;
    const E_COULDNT_CONNECT = 7;
    const E_FTP_WEIRD_SERVER_REPLY = 8;
    const E_REMOTE_ACCESS_DENIED = 9;
    const E_FTP_ACCEPT_FAILED = 10;
    const E_FTP_WEIRD_PASS_REPLY = 11;
    const E_FTP_ACCEPT_TIMEOUT = 12;
    const E_FTP_WEIRD_PASV_REPLY = 13;
    const E_FTP_WEIRD_227_FORMAT = 14;
    const E_FTP_CANT_GET_HOST = 15;
    const E_HTTP2 = 16;
    const E_FTP_COULDNT_SET_TYPE = 17;
    const E_PARTIAL_FILE = 18;
    const E_FTP_COULDNT_RETR_FILE = 19;
    const E_QUOTE_ERROR = 21;
    const E_HTTP_RETURNED_ERROR = 22;
    const E_WRITE_ERROR = 23;
    const E_UPLOAD_FAILED = 25;
    const E_READ_ERROR = 26;
    const E_OUT_OF_MEMORY = 27;
    const E_OPERATION_TIMEDOUT = 28;
    const E_FTP_PORT_FAILED = 30;
    const E_FTP_COULDNT_USE_REST = 31;
    const E_RANGE_ERROR = 33;
    const E_HTTP_POST_ERROR = 34;
    const E_SSL_CONNECT_ERROR = 35;
    const E_BAD_DOWNLOAD_RESUME = 36;
    const E_FILE_COULDNT_READ_FILE = 37;
    const E_LDAP_CANNOT_BIND  = 38;
    const E_LDAP_SEARCH_FAILED = 39;
    const E_FUNCTION_NOT_FOUND = 41;
    const E_ABORTED_BY_CALLBACK = 42;
    const E_BAD_FUNCTION_ARGUMENT = 43;
    const E_INTERFACE_FAILED = 45;
    const E_TOO_MANY_REDIRECTS = 47;
    const E_UNKNOWN_OPTION = 48;
    const E_TELNET_OPTION_SYNTAX = 49;
    const E_PEER_FAILED_VERIFICATION = 51;
    const E_GOT_NOTHING = 52;
    const E_SSL_ENGINE_NOTFOUND = 53;
    const E_SSL_ENGINE_SETFAILED = 54;
    const E_SEND_ERROR = 55;
    const E_RECV_ERROR = 56;
    const E_SSL_CERTPROBLEM = 58;
    const E_SSL_CIPHER = 59;
    const E_SSL_CACERT = 60;
    const E_BAD_CONTENT_ENCODING = 61;
    const E_LDAP_INVALID_URL = 62;
    const E_FILESIZE_EXCEEDED = 63;
    const E_USE_SSL_FAILED = 64;
    const E_SEND_FAIL_REWIND = 65;
    const E_SSL_ENGINE_INITFAILED = 66;
    const E_LOGIN_DENIED = 67;
    const E_TFTP_NOTFOUND = 68;
    const E_TFTP_PERM = 69;
    const E_REMOTE_DISK_FULL = 70;
    const E_TFTP_ILLEGAL = 71;
    const E_TFTP_UNKNOWNID = 72;
    const E_REMOTE_FILE_EXISTS = 73;
    const E_TFTP_NOSUCHUSER = 74;
    const E_CONV_FAILED = 75;
    const E_CONV_REQD = 76;
    const E_SSL_CACERT_BADFILE = 77;
    const E_REMOTE_FILE_NOT_FOUND = 78;
    const E_SSH = 79;
    const E_SSL_SHUTDOWN_FAILED = 80;
    const E_SSL_CRL_BADFILE = 82;
    const E_SSL_ISSUER_ERROR = 83;
    const E_FTP_PRET_FAILED = 84;
    const E_RTSP_CSEQ_ERROR = 85;
    const E_RTSP_SESSION_ERROR = 86;
    const E_FTP_BAD_FILE_LIST = 87;
    const E_CHUNK_FAILED = 88;
    const E_NO_CONNECTION_AVAILABLE = 89;
    const E_SSL_PINNEDPUBKEYNOTMATCH = 90;
    const E_SSL_INVALIDCERTSTATUS = 91;

    /* CURLMcode */
    const M_OK = 0;
    const M_BAD_HANDLE = 1;
    const M_BAD_EASY_HANDLE = 2;
    const M_OUT_OF_MEMORY = 3;
    const M_INTERNAL_ERROR = 4;
    const M_BAD_SOCKET = 5;
    const M_UNKNOWN_OPTION = 6;
    const M_ADDED_ALREADY = 7;

    /* CURLSHcode */
    const SHE_OK = 0;
    const SHE_BAD_OPTION = 1;
    const SHE_IN_USE = 2;
    const SHE_INVALID = 3;
    const SHE_NOMEM = 4;
    const SHE_NOT_BUILT_IN = 5;

    /** @var  int */
    private $errno;

    /** @var  string */
    private $function;

    /** @var  string */
    private $message;

    /**
     * Constructs CurlError object
     *
     * @param int $errno
     * @param string $function
     */
    public function __construct($errno, $function)
    {
        $this->errno = $errno;
        $this->function = $function;
        $this->message = "[$function]" . curl_strerror($errno);
    }

    /**
     * Get error number
     *
     * @return int
     */
    public function getNo()
    {
        return $this->errno;
    }

    /**
     * Get function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

}