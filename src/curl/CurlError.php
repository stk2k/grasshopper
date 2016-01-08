<?php
namespace Grasshopper\curl;


class CurlError
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
    private $curl_function;

    /** @var  string */
    private $errmsg;

    /** @var  string */
    private $category;

    /**
     * Constructs CurlError object
     *
     * @param int $errno
     * @param string $curl_function
     */
    public function __construct($errno, $curl_function)
    {
        $this->errno = $errno;
        $this->curl_function = $curl_function;
        $this->category = self::detectCategory($curl_function);
        $this->errmsg = '[' . $this->category . ']' . curl_strerror($errno) . ': ' . $curl_function;;
    }

    /**
     * Get error number
     *
     * @return int
     */
    public function getNumber()
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
        return $this->curl_function;
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->errmsg;
    }

    /**
     * Get error category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Detect category from function
     *
     * @param string $function
     *
     * @return string
     */
    private static function detectCategory($function)
    {
        static $defs = [
            'curl_close' => self::CATEGORY_CURL,
            'curl_copy_handle' => self::CATEGORY_CURL,
            'curl_errno' => self::CATEGORY_CURL,
            'curl_error' => self::CATEGORY_CURL,
            'curl_escape' => self::CATEGORY_CURL,
            'curl_exec' => self::CATEGORY_CURL,
            'curl_file_create' => self::CATEGORY_CURL,
            'curl_getinfo' => self::CATEGORY_CURL,
            'curl_init' => self::CATEGORY_CURL,
            'curl_multi_add_handle' => self::CATEGORY_CURL_MULTI,
            'curl_multi_close' => self::CATEGORY_CURL_MULTI,
            'curl_multi_exec' => self::CATEGORY_CURL_MULTI,
            'curl_multi_getcontent' => self::CATEGORY_CURL_MULTI,
            'curl_multi_info_read' => self::CATEGORY_CURL_MULTI,
            'curl_multi_init' => self::CATEGORY_CURL_MULTI,
            'curl_multi_remove_handle' => self::CATEGORY_CURL_MULTI,
            'curl_multi_select' => self::CATEGORY_CURL_MULTI,
            'curl_multi_setopt' => self::CATEGORY_CURL_MULTI,
            'curl_multi_strerror' => self::CATEGORY_CURL_MULTI,
            'curl_pause' => self::CATEGORY_CURL,
            'curl_reset' => self::CATEGORY_CURL,
            'curl_setopt_array' => self::CATEGORY_CURL,
            'curl_setopt' => self::CATEGORY_CURL,
            'curl_share_close' => self::CATEGORY_CURL_SHARE,
            'curl_share_init' => self::CATEGORY_CURL_SHARE,
            'curl_share_setopt' => self::CATEGORY_CURL_SHARE,
            'curl_strerror' => self::CATEGORY_CURL,
            'curl_unescape' => self::CATEGORY_CURL,
            'curl_version' => self::CATEGORY_CURL,
        ];
        return isset($defs[$function]) ? $defs[$function] : '';
    }
    
    /**
     * Get cURL error code string
     *
     * @param string $category
     * @param string $errno
     *
     * @return string
     */
    private static function getErrorCodeString($category, $errno)
    {
        /* CURLcode */
        static $defs = [
            self::CATEGORY_CURL => [
                self::E_OK => 'E_OK',
                self::E_UNSUPPORTED_PROTOCOL => 'E_OK',
                self::E_FAILED_INIT => 'E_OK',
                self::E_URL_MALFORMAT => 'E_OK',
                self::E_NOT_BUILT_IN => 'E_OK',
                self::E_COULDNT_RESOLVE_PROXY => 'E_OK',
                self::E_COULDNT_RESOLVE_HOST => 'E_OK',
                self::E_COULDNT_CONNECT => 'E_OK',
                self::E_FTP_WEIRD_SERVER_REPLY => 'E_OK',
                self::E_REMOTE_ACCESS_DENIED => 'E_OK',
                self::E_FTP_ACCEPT_FAILED => 'E_OK',
                self::E_FTP_WEIRD_PASS_REPLY => 'E_OK',
                self::E_FTP_ACCEPT_TIMEOUT => 'E_OK',
                self::E_FTP_WEIRD_PASV_REPLY => 'E_OK',
                self::E_FTP_WEIRD_227_FORMAT => 'E_OK',
                self::E_FTP_CANT_GET_HOST => 'E_OK',
                self::E_HTTP2 => 'E_OK',
                self::E_FTP_COULDNT_SET_TYPE => 'E_OK',
                self::E_PARTIAL_FILE => 'E_OK',
                self::E_FTP_COULDNT_RETR_FILE => 'E_OK',
                self::E_QUOTE_ERROR => 'E_OK',
                self::E_HTTP_RETURNED_ERROR => 'E_OK',
                self::E_WRITE_ERROR => 'E_OK',
                self::E_UPLOAD_FAILED => 'E_OK',
                self::E_READ_ERROR => 'E_OK',
                self::E_OUT_OF_MEMORY => 'E_OK',
                self::E_OPERATION_TIMEDOUT => 'E_OK',
                self::E_FTP_PORT_FAILED => 'E_OK',
                self::E_FTP_COULDNT_USE_REST => 'E_OK',
                self::E_RANGE_ERROR => 'E_OK',
                self::E_HTTP_POST_ERROR => 'E_OK',
                self::E_SSL_CONNECT_ERROR => 'E_OK',
                self::E_BAD_DOWNLOAD_RESUME => 'E_OK',
                self::E_FILE_COULDNT_READ_FILE => 'E_OK',
                self::E_LDAP_CANNOT_BIND => 'E_OK',
                self::E_LDAP_SEARCH_FAILED => 'E_OK',
                self::E_FUNCTION_NOT_FOUND => 'E_OK',
                self::E_ABORTED_BY_CALLBACK => 'E_OK',
                self::E_BAD_FUNCTION_ARGUMENT => 'E_OK',
                self::E_INTERFACE_FAILED => 'E_OK',
                self::E_TOO_MANY_REDIRECTS => 'E_OK',
                self::E_UNKNOWN_OPTION => 'E_OK',
                self::E_TELNET_OPTION_SYNTAX => 'E_OK',
                self::E_PEER_FAILED_VERIFICATION => 'E_OK',
                self::E_GOT_NOTHING  => 'E_OK',
                self::E_SSL_ENGINE_NOTFOUND => 'E_OK',
                self::E_SSL_ENGINE_SETFAILED => 'E_OK',
                self::E_SEND_ERROR  => 'E_OK',
                self::E_RECV_ERROR => 'E_OK',
                self::E_SSL_CERTPROBLEM => 'E_OK',
                self::E_SSL_CIPHER => 'E_OK',
                self::E_SSL_CACERT  => 'E_OK',
                self::E_BAD_CONTENT_ENCODING => 'E_OK',
                self::E_LDAP_INVALID_URL => 'E_OK',
                self::E_FILESIZE_EXCEEDED => 'E_OK',
                self::E_USE_SSL_FAILED => 'E_OK',
                self::E_SEND_FAIL_REWIND => 'E_OK',
                self::E_SSL_ENGINE_INITFAILED => 'E_OK',
                self::E_LOGIN_DENIED => 'E_OK',
                self::E_TFTP_NOTFOUND => 'E_OK',
                self::E_TFTP_PERM => 'E_OK',
                self::E_REMOTE_DISK_FULL => 'E_OK',
                self::E_TFTP_ILLEGAL => 'E_OK',
                self::E_TFTP_UNKNOWNID => 'E_OK',
                self::E_REMOTE_FILE_EXISTS => 'E_OK',
                self::E_TFTP_NOSUCHUSER => 'E_OK',
                self::E_CONV_FAILED => 'E_OK',
                self::E_CONV_REQD  => 'E_OK',
                self::E_SSL_CACERT_BADFILE => 'E_OK',
                self::E_REMOTE_FILE_NOT_FOUND => 'E_OK',
                self::E_SSH => 'E_OK',
                self::E_SSL_SHUTDOWN_FAILED => 'E_OK',
                self::E_SSL_CRL_BADFILE => 'E_OK',
                self::E_SSL_ISSUER_ERROR => 'E_OK',
                self::E_FTP_PRET_FAILED => 'E_OK',
                self::E_RTSP_CSEQ_ERROR => 'E_OK',
                self::E_RTSP_SESSION_ERROR => 'E_OK',
                self::E_FTP_BAD_FILE_LIST => 'E_OK',
                self::E_CHUNK_FAILED => 'E_OK',
                self::E_NO_CONNECTION_AVAILABLE => 'E_OK',
                self::E_SSL_PINNEDPUBKEYNOTMATCH  => 'E_OK',
                self::E_SSL_INVALIDCERTSTATUS => 'E_OK',
            ],
            /* CURLMcode */
            self::CATEGORY_CURL_MULTI => [
                self::M_OK => 'E_OK',
                self::M_BAD_HANDLE  => 'E_OK',
                self::M_BAD_EASY_HANDLE  => 'E_OK',
                self::M_OUT_OF_MEMORY => 'E_OK',
                self::M_INTERNAL_ERROR => 'E_OK',
                self::M_BAD_SOCKET => 'E_OK',
                self::M_UNKNOWN_OPTION => 'E_OK',
                self::M_ADDED_ALREADY => 'E_OK',
            ],
            /* CURLSHcode */
            self::CATEGORY_CURL_SHARE => [
                self::SHE_OK  => 'E_OK',
                self::SHE_BAD_OPTION => 'E_OK',
                self::SHE_IN_USE => 'E_OK',
                self::SHE_INVALID => 'E_OK',
                self::SHE_NOMEM => 'E_OK',
                self::SHE_NOT_BUILT_IN => 'E_OK',
            ],
        ];
        return isset($defs[$category][$errno]) ? $defs[$category][$errno] : '';
    }
}