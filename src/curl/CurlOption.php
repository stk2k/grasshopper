<?php
namespace Grasshopper\curl;

use Grasshopper\Grasshopper;
use Grasshopper\exception\GrasshopperException;

class CurlOption
{
    
    /**
     * Convert option code into string name
     *
     * @param integer $option
     *
     * @return string
     */
    public static function getString($option)
    {
        switch($option)
        {
            case CURLOPT_COOKIESESSION:   return 'CURLOPT_COOKIESESSION';
            case CURLOPT_CERTINFO:   return 'CURLOPT_CERTINFO';
            case CURLOPT_CONNECT_ONLY:   return 'CURLOPT_CONNECT_ONLY';
            case CURLOPT_CRLF:   return 'CURLOPT_CRLF';
            case CURLOPT_DNS_USE_GLOBAL_CACHE:   return 'CURLOPT_DNS_USE_GLOBAL_CACHE';
            case CURLOPT_FAILONERROR:   return 'CURLOPT_FAILONERROR';
            case CURLOPT_FILETIME:   return 'CURLOPT_FILETIME';
            case CURLOPT_FOLLOWLOCATION:   return 'CURLOPT_FOLLOWLOCATION';
            case CURLOPT_FORBID_REUSE:   return 'CURLOPT_FORBID_REUSE';
            case CURLOPT_FRESH_CONNECT:   return 'CURLOPT_FRESH_CONNECT';
            case CURLOPT_FTP_USE_EPSV:   return 'CURLOPT_FTP_USE_EPSV';
            case CURLOPT_FTP_CREATE_MISSING_DIRS:   return 'CURLOPT_FTP_CREATE_MISSING_DIRS';
            case CURLOPT_FTPAPPEND:   return 'CURLOPT_FTPAPPEND';
            case CURLOPT_TCP_NODELAY:   return 'CURLOPT_TCP_NODELAY';
            //case CURLOPT_FTPASCII:   return 'CURLOPT_FTPASCII';
            case CURLOPT_FTPLISTONLY:   return 'CURLOPT_FTPLISTONLY';
            case CURLOPT_HEADER:   return 'CURLOPT_HEADER';
            //case CURLINFO_HEADER_OUT:   return 'CURLINFO_HEADER_OUT';
            case CURLOPT_HTTPGET:   return 'CURLOPT_HTTPGET';
            case CURLOPT_HTTPPROXYTUNNEL:   return 'CURLOPT_HTTPPROXYTUNNEL';
            //case CURLOPT_MUTE:   return 'CURLOPT_MUTE';
            case CURLOPT_NETRC:   return 'CURLOPT_NETRC';
            case CURLOPT_NOBODY:   return 'CURLOPT_NOBODY';
            case CURLOPT_NOPROGRESS:   return 'CURLOPT_NOPROGRESS';
            case CURLOPT_POST:   return 'CURLOPT_POST';
            case CURLOPT_PUT:   return 'CURLOPT_PUT';
            case CURLOPT_RETURNTRANSFER:   return 'CURLOPT_RETURNTRANSFER';
            case CURLOPT_SAFE_UPLOAD:   return 'CURLOPT_SAFE_UPLOAD';
            case CURLOPT_SSL_VERIFYPEER:   return 'CURLOPT_SSL_VERIFYPEER';
            case CURLOPT_TRANSFERTEXT:   return 'CURLOPT_TRANSFERTEXT';
            case CURLOPT_UPLOAD:   return 'CURLOPT_UPLOAD';
            case CURLOPT_VERBOSE:   return 'CURLOPT_VERBOSE';
            //case CURLOPT_CLOSEPOLICY:   return 'CURLOPT_CLOSEPOLICY';
            case CURLOPT_CONNECTTIMEOUT:   return 'CURLOPT_CONNECTTIMEOUT';
            case CURLOPT_CONNECTTIMEOUT_MS:   return 'CURLOPT_CONNECTTIMEOUT_MS';
            case CURLOPT_DNS_CACHE_TIMEOUT:   return 'CURLOPT_DNS_CACHE_TIMEOUT';
            case CURLOPT_HTTP_VERSION:   return 'CURLOPT_HTTP_VERSION';
            case CURLOPT_INFILESIZE:   return 'CURLOPT_INFILESIZE';
            case CURLOPT_LOW_SPEED_LIMIT:   return 'CURLOPT_LOW_SPEED_LIMIT';
            case CURLOPT_LOW_SPEED_TIME:   return 'CURLOPT_LOW_SPEED_TIME';
            case CURLOPT_MAXCONNECTS:   return 'CURLOPT_MAXCONNECTS';
            case CURLOPT_MAXREDIRS:   return 'CURLOPT_MAXREDIRS';
            case CURLOPT_PORT:   return 'CURLOPT_PORT';
            case CURLOPT_POSTREDIR:   return 'CURLOPT_POSTREDIR';
            case CURLOPT_PROTOCOLS:   return 'CURLOPT_PROTOCOLS';
            case CURLOPT_RESUME_FROM:   return 'CURLOPT_RESUME_FROM';
            case CURLOPT_SSL_VERIFYHOST:   return 'CURLOPT_SSL_VERIFYHOST';
            case CURLOPT_SSLVERSION:   return 'CURLOPT_SSLVERSION';
            case CURLOPT_TIMEOUT:   return 'CURLOPT_TIMEOUT';
            case CURLOPT_TIMEOUT_MS:   return 'CURLOPT_TIMEOUT_MS';
            case CURLOPT_TIMEVALUE:   return 'CURLOPT_TIMEVALUE';
            case CURLOPT_MAX_RECV_SPEED_LARGE:   return 'CURLOPT_MAX_RECV_SPEED_LARGE';
            case CURLOPT_MAX_SEND_SPEED_LARGE:   return 'CURLOPT_MAX_SEND_SPEED_LARGE';
            case CURLOPT_SSH_AUTH_TYPES:   return 'CURLOPT_SSH_AUTH_TYPES';
            case CURLOPT_IPRESOLVE:   return 'CURLOPT_IPRESOLVE';
            case CURLOPT_FTP_FILEMETHOD:   return 'CURLOPT_FTP_FILEMETHOD';
            case CURLOPT_CAINFO:   return 'CURLOPT_CAINFO';
            case CURLOPT_CAPATH:   return 'CURLOPT_CAPATH';
            case CURLOPT_COOKIE:   return 'CURLOPT_COOKIE';
            case CURLOPT_COOKIEFILE:   return 'CURLOPT_COOKIEFILE';
            case CURLOPT_COOKIEJAR:   return 'CURLOPT_COOKIEJAR';
            case CURLOPT_CUSTOMREQUEST:   return 'CURLOPT_CUSTOMREQUEST';
            case CURLOPT_EGDSOCKET:   return 'CURLOPT_EGDSOCKET';
            case CURLOPT_ENCODING:   return 'CURLOPT_ENCODING';
            case CURLOPT_FTPPORT:   return 'CURLOPT_FTPPORT';
            case CURLOPT_INTERFACE:   return 'CURLOPT_INTERFACE';
            case CURLOPT_KEYPASSWD:   return 'CURLOPT_KEYPASSWD';
            case CURLOPT_KRB4LEVEL:   return 'CURLOPT_KRB4LEVEL';
            case CURLOPT_POSTFIELDS:   return 'CURLOPT_POSTFIELDS';
            case CURLOPT_PROXY:   return 'CURLOPT_PROXY';
            case CURLOPT_PROXYUSERPWD:   return 'CURLOPT_PROXYUSERPWD';
            case CURLOPT_RANDOM_FILE:   return 'CURLOPT_RANDOM_FILE';
            case CURLOPT_RANGE:   return 'CURLOPT_RANGE';
            case CURLOPT_REFERER:   return 'CURLOPT_REFERER';
            case CURLOPT_SSH_HOST_PUBLIC_KEY_MD5:   return 'CURLOPT_SSH_HOST_PUBLIC_KEY_MD5';
            case CURLOPT_SSH_PUBLIC_KEYFILE:   return 'CURLOPT_SSH_PUBLIC_KEYFILE';
            case CURLOPT_SSH_PRIVATE_KEYFILE:   return 'CURLOPT_SSH_PRIVATE_KEYFILE';
            case CURLOPT_SSL_CIPHER_LIST:   return 'CURLOPT_SSL_CIPHER_LIST';
            case CURLOPT_SSLCERT:   return 'CURLOPT_SSLCERT';
            case CURLOPT_SSLCERTPASSWD:   return 'CURLOPT_SSLCERTPASSWD';
            case CURLOPT_SSLENGINE:   return 'CURLOPT_SSLENGINE';
            case CURLOPT_SSLENGINE_DEFAULT:   return 'CURLOPT_SSLENGINE_DEFAULT';
            case CURLOPT_SSLKEY:   return 'CURLOPT_SSLKEY';
            case CURLOPT_SSLKEYPASSWD:   return 'CURLOPT_SSLKEYPASSWD';
            case CURLOPT_SSLKEYTYPE:   return 'CURLOPT_SSLKEYTYPE';
            case CURLOPT_URL:   return 'CURLOPT_URL';
            case CURLOPT_USERAGENT:   return 'CURLOPT_USERAGENT';
            case CURLOPT_USERNAME:   return 'CURLOPT_USERNAME';
            case CURLOPT_USERPWD:   return 'CURLOPT_USERPWD';
            case CURLOPT_HTTPHEADER:   return 'CURLOPT_HTTPHEADER';
            case CURLOPT_POSTQUOTE:   return 'CURLOPT_POSTQUOTE';
            case CURLOPT_QUOTE:   return 'CURLOPT_QUOTE';
            case CURLOPT_FILE:   return 'CURLOPT_FILE';
            case CURLOPT_INFILE:   return 'CURLOPT_INFILE';
            case CURLOPT_STDERR:   return 'CURLOPT_STDERR';
            case CURLOPT_WRITEHEADER:   return 'CURLOPT_WRITEHEADER';
            case CURLOPT_HEADERFUNCTION:   return 'CURLOPT_HEADERFUNCTION';
            //case CURLOPT_PASSWDFUNCTION:   return 'CURLOPT_PASSWDFUNCTION';
            case CURLOPT_READFUNCTION:   return 'CURLOPT_READFUNCTION';
            case CURLOPT_WRITEFUNCTION:   return 'CURLOPT_WRITEFUNCTION';
        }
        if ( version_compare(PHP_VERSION,'5.0.0') >= 0 ) {
            switch ($option) {
                case CURLOPT_FTP_USE_EPRT:   return 'CURLOPT_FTP_USE_EPRT';
                case CURLOPT_NOSIGNAL:   return 'CURLOPT_NOSIGNAL';
                case CURLOPT_UNRESTRICTED_AUTH:   return 'CURLOPT_UNRESTRICTED_AUTH';
                case CURLOPT_BUFFERSIZE:   return 'CURLOPT_BUFFERSIZE';
                case CURLOPT_HTTPAUTH:   return 'CURLOPT_HTTPAUTH';
                case CURLOPT_PROXYPORT:   return 'CURLOPT_PROXYPORT';
                case CURLOPT_PROXYTYPE:   return 'CURLOPT_PROXYTYPE';
                case CURLOPT_SSLCERTTYPE:   return 'CURLOPT_SSLCERTTYPE';
                case CURLOPT_HTTP200ALIASES:   return 'CURLOPT_HTTP200ALIASES';
            }
        }
        if ( version_compare(PHP_VERSION,'5.1.0') >= 0 ) {
            switch ($option) {
                case CURLOPT_AUTOREFERER:   return 'CURLOPT_AUTOREFERER';
                case CURLOPT_BINARYTRANSFER:   return 'CURLOPT_BINARYTRANSFER';
                case CURLOPT_FTPSSLAUTH:   return 'CURLOPT_FTPSSLAUTH';
                case CURLOPT_PROXYAUTH:   return 'CURLOPT_PROXYAUTH';
                case CURLOPT_TIMECONDITION:   return 'CURLOPT_TIMECONDITION';
            }
        }
        if ( version_compare(PHP_VERSION,'5.2.4') >= 0 ) {
            switch ($option) {
                case CURLOPT_PRIVATE:   return 'CURLOPT_PRIVATE';
            }
        }
        if ( version_compare(PHP_VERSION,'5.2.10') >= 0 ) {
            switch ($option) {
                case CURLOPT_PROTOCOLS :   return 'CURLOPT_PROTOCOLS ';
                case CURLOPT_REDIR_PROTOCOLS:   return 'CURLOPT_REDIR_PROTOCOLS';
            }
        }
        if ( version_compare(PHP_VERSION,'5.3.0') >= 0 ) {
            switch ($option) {
                case CURLOPT_PROGRESSFUNCTION:   return 'CURLOPT_PROGRESSFUNCTION';
            }
        }
        if ( version_compare(PHP_VERSION,'5.5.0') >= 0 ) {
            switch ($option) {
                case CURLOPT_SHARE:   return 'CURLOPT_SHARE';
            }
        }
        if ( version_compare(PHP_VERSION,'7.0.7') >= 0 ){
            switch($option)
            {
                case CURLOPT_SERVICE_NAME:   return 'CURLOPT_SERVICE_NAME';
                case CURLOPT_PROXYHEADER:   return 'CURLOPT_PROXYHEADER';
                case CURLOPT_XOAUTH2_BEARER:   return 'CURLOPT_XOAUTH2_BEARER';
                case CURLOPT_UNIX_SOCKET_PATH:   return 'CURLOPT_UNIX_SOCKET_PATH';
                case CURLOPT_PROXY_SERVICE_NAME:   return 'CURLOPT_PROXY_SERVICE_NAME';
                case CURLOPT_PINNEDPUBLICKEY:   return 'CURLOPT_PINNEDPUBLICKEY';
                case CURLOPT_LOGIN_OPTIONS:   return 'CURLOPT_LOGIN_OPTIONS';
                case CURLOPT_HEADEROPT:   return 'CURLOPT_HEADEROPT';
                case CURLOPT_EXPECT_100_TIMEOUT_MS:   return 'CURLOPT_EXPECT_100_TIMEOUT_MS';
                case CURLOPT_SSL_VERIFYSTATUS:   return 'CURLOPT_SSL_VERIFYSTATUS';
                case CURLOPT_SASL_IR:   return 'CURLOPT_SASL_IR';
                case CURLOPT_SSL_ENABLE_ALPN:   return 'CURLOPT_SSL_ENABLE_ALPN';
                case CURLOPT_SSL_ENABLE_NPN:   return 'CURLOPT_SSL_ENABLE_NPN';
                case CURLOPT_PATH_AS_IS:   return 'CURLOPT_PATH_AS_IS';
                case CURLOPT_PIPEWAIT:   return 'CURLOPT_PIPEWAIT';
                case CURLOPT_SSL_FALSESTART:   return 'CURLOPT_SSL_FALSESTART';
                case CURLOPT_TCP_FASTOPEN:   return 'CURLOPT_TCP_FASTOPEN';
                case CURLOPT_TFTP_NO_OPTIONS:   return 'CURLOPT_TFTP_NO_OPTIONS';
                case CURLOPT_SSL_OPTIONS:   return 'CURLOPT_SSL_OPTIONS';
                case CURLOPT_STREAM_WEIGHT:   return 'CURLOPT_STREAM_WEIGHT';
                case CURLOPT_DEFAULT_PROTOCOL:   return 'CURLOPT_DEFAULT_PROTOCOL';
                case CURLOPT_DNS_INTERFACE:   return 'CURLOPT_DNS_INTERFACE';
                case CURLOPT_DNS_LOCAL_IP4:   return 'CURLOPT_DNS_LOCAL_IP4';
                case CURLOPT_DNS_LOCAL_IP6:   return 'CURLOPT_DNS_LOCAL_IP6';
                case CURLOPT_CONNECT_TO:   return 'CURLOPT_CONNECT_TO';
                case CURL_HTTP_VERSION_2:   return 'CURL_HTTP_VERSION_2';
                case CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE:   return 'CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE';
                case CURL_HTTP_VERSION_2TLS:   return 'CURL_HTTP_VERSION_2TLS';
                //case CURL_REDIR_POST_301:   return 'CURL_REDIR_POST_301';
                //case CURL_REDIR_POST_302:   return 'CURL_REDIR_POST_302';
                //case CURL_REDIR_POST_303:   return 'CURL_REDIR_POST_303';
                //case CURL_REDIR_POST_ALL:   return 'CURL_REDIR_POST_ALL';
                //case CURL_VERSION_KERBEROS5:   return 'CURL_VERSION_KERBEROS5';
                //case CURL_VERSION_PSL:   return 'CURL_VERSION_PSL';
                //case CURL_VERSION_UNIX_SOCKETS:   return 'CURL_VERSION_UNIX_SOCKETS';
                case CURLAUTH_NEGOTIATE:   return 'CURLAUTH_NEGOTIATE';
                case CURLAUTH_NTLM_WB:   return 'CURLAUTH_NTLM_WB';
                //case CURLFTP_CREATE_DIR:   return 'CURLFTP_CREATE_DIR';
                case CURLFTP_CREATE_DIR_NONE:   return 'CURLFTP_CREATE_DIR_NONE';
                case CURLFTP_CREATE_DIR_RETRY:   return 'CURLFTP_CREATE_DIR_RETRY';
                //case CURLHEADER_SEPARATE:   return 'CURLHEADER_SEPARATE';
                case CURLHEADER_UNIFIED:   return 'CURLHEADER_UNIFIED';
                case CURLMOPT_CHUNK_LENGTH_PENALTY_SIZE:   return 'CURLMOPT_CHUNK_LENGTH_PENALTY_SIZE';
                case CURLMOPT_CONTENT_LENGTH_PENALTY_SIZE:   return 'CURLMOPT_CONTENT_LENGTH_PENALTY_SIZE';
                case CURLMOPT_MAX_HOST_CONNECTIONS:   return 'CURLMOPT_MAX_HOST_CONNECTIONS';
                case CURLMOPT_MAX_PIPELINE_LENGTH:   return 'CURLMOPT_MAX_PIPELINE_LENGTH';
                case CURLMOPT_MAX_TOTAL_CONNECTIONS:   return 'CURLMOPT_MAX_TOTAL_CONNECTIONS';
                case CURLOPT_XOAUTH2_BEARER:   return 'CURLOPT_XOAUTH2_BEARER';
                case CURLPROTO_SMB:   return 'CURLPROTO_SMB';
                case CURLPROTO_SMBS:   return 'CURLPROTO_SMBS';
                //case CURLPROXY_HTTP_1_0:   return 'CURLPROXY_HTTP_1_0';
                case CURLSSH_AUTH_AGENT:   return 'CURLSSH_AUTH_AGENT';
                //case CURLSSLOPT_NO_REVOKE:   return 'CURLSSLOPT_NO_REVOKE';
            }
        }
        throw new GrasshopperException('Invalid option:'.$option, Grasshopper::ERROR_INVALID_OPTION);
    }
}
