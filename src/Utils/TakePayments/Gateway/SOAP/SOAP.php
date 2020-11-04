<?php

namespace App\Utils\TakePayments\Gateway\SOAP;

use App\Utils\TakePayments\Gateway\Common\SharedFunctions;
use App\Utils\TakePayments\Gateway\Common\StringList;
use App\Utils\TakePayments\Gateway\Common\XmlParser;
use Exception;

class SOAP
{
    /**
     * @var string
     */
    private $m_szMethod;
    /**
     * @var string
     */
    private $m_szMethodURI;
    /**
     * @var string
     */
    private $m_szURL;
    /**
     * @var string
     */
    private $m_szActionURI;
    /**
     * @var string
     */
    private $m_szSOAPEncoding;
    /**
     * @var bool
     */
    private $m_boPacketBuilt;
    /**
     * @var string
     */
    private $m_szLastResponse;
    /**
     * @var string
     */
    private $m_szSOAPPacket;
    /**
     * @var XmlParser
     */
    private $m_xmlParser;
    /**
     * @var XMLTag
     */
    private $m_xmlTag;
    /**
     * @var int
     */
    private $m_nTimeout;
    /**
     * @var Exception
     */
    private $m_eLastException;
    /**
     * @var SOAPNamespaceList
     */
    private $m_lsnSOAPNamespaceList;
    /**
     * @var SOAPParamList
     */
    private $m_lspSOAPParamList;

    private $certDir;

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->m_szMethod;
    }

    /**
     * @return string
     */
    public function getMethodURI()
    {
        return $this->m_szMethodURI;
    }

    /**
     * @return string
     */
    public function getURL()
    {
        return $this->m_szURL;
    }

    /**
     * @param $value
     */
    public function setURL($value)
    {
        $this->m_szURL = $value;
    }

    /**
     * @return string
     */
    public function getActionURI()
    {
        return $this->m_szActionURI;
    }

    /**
     * @return string
     */
    public function getSOAPEncoding()
    {
        return $this->m_szSOAPEncoding;
    }

    /**
     * @return bool
     */
    public function getPacketBuilt()
    {
        return $this->m_boPacketBuilt;
    }

    /**
     * @return string
     */
    public function getLastResponse()
    {
        return $this->m_szLastResponse;
    }

    /**
     * @return string
     */
    public function getSOAPPacket()
    {
        return $this->m_szSOAPPacket;
    }

    /**
     * @return XmlParser
     */
    public function getXmlParser()
    {
        return $this->m_xmlParser;
    }

    /**
     * @return XMLTag
     */
    public function getXmlTag()
    {
        return $this->m_xmlTag;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->m_nTimeout;
    }

    /**
     * @param int $value
     */
    public function setTimeout($value)
    {
        $this->m_nTimeout = $value;
    }

    /**
     * @return Exception
     */
    public function getLastException()
    {
        $this->m_eLastException;
    }

    /**
     * Get the value of certDir.
     */
    public function getCertDir()
    {
        return $this->certDir;
    }

    /**
     * Set the value of certDir.
     *
     * @return self
     */
    public function setCertDir($certDir)
    {
        $this->certDir = $certDir;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function buildPacket()
    {
        $sbString = null;
        $sbString2 = null;
        $snNamespace = null;
        $szFirstNamespace = null;
        $szFirstPrefix = null;
        $nCount = 0;
        $spSOAPParam = null;

        // build the xml SOAP request
        // start with the XML version
        $sbString = '';
        $sbString .= '<?xml version="1.0" encoding="utf-8" ?>';

        if (0 == $this->m_lsnSOAPNamespaceList->getCount()) {
            $szFirstNamespace = 'http://schemas.xmlsoap.org/soap/envelope/';
            $szFirstPrefix = 'soap';
        } else {
            $snNamespace = $this->m_lsnSOAPNamespaceList->getAt(0);

            if (null == $snNamespace) {
                $szFirstNamespace = 'http://schemas.xmlsoap.org/soap/envelope/';
                $szFirstPrefix = 'soap';
            } else {
                if (null == $snNamespace->getNamespace() ||
                    '' == $snNamespace->getNamespace()
                ) {
                    $szFirstNamespace =
                        'http://schemas.xmlsoap.org/soap/envelope/';
                } else {
                    $szFirstNamespace = $snNamespace->getNamespace();
                }

                if (null == $snNamespace->getPrefix() ||
                    '' == $snNamespace->getPrefix()
                ) {
                    $szFirstPrefix = 'soap';
                } else {
                    $szFirstPrefix = $snNamespace->getPrefix();
                }
            }
        }

        $sbString2 = '';
        $sbString2 .=
            '<'.
            $szFirstPrefix.
            ':Envelope xmlns:'.
            $szFirstPrefix.
            '="'.
            $szFirstNamespace.
            '"';

        for ($nCount = 1; $nCount < $this->m_lsnSOAPNamespaceList->getCount(); ++$nCount
        ) {
            $snNamespace = $this->m_lsnSOAPNamespaceList->getAt($nCount);

            if (null != $snNamespace) {
                if ('' != $snNamespace->getNamespace() &&
                    '' != $snNamespace->getPrefix()
                ) {
                    $sbString2 .=
                        ' xmlns:'.
                        $snNamespace->getPrefix().
                        '="'.
                        $snNamespace->getNamespace().
                        '"';
                }
            }
        }

        $sbString2 .= '>';

        $sbString .= (string) $sbString2;
        $sbString2 = '';
        $sbString2 .= '<'.$szFirstPrefix.':Body>';
        $sbString .= (string) $sbString2;
        $sbString2 = '';
        $sbString2 .=
            '<'.
            $this->getMethod().
            ' xmlns="'.
            $this->getMethodURI().
            '">';
        $sbString .= (string) $sbString2;

        for ($nCount = 0; $nCount < $this->m_lspSOAPParamList->getCount(); ++$nCount
        ) {
            $spSOAPParam = $this->m_lspSOAPParamList->getAt($nCount);

            if (null != $spSOAPParam) {
                $sbString .= $spSOAPParam->toXMLString();
            }
        }

        $sbString2 = '';
        $sbString2 .= '</'.$this->getMethod().'>';
        $sbString .= (string) $sbString2;
        $sbString2 = '';
        $sbString2 .=
            '</'.$szFirstPrefix.':Body></'.$szFirstPrefix.':Envelope>';
        $sbString .= (string) $sbString2;

        $this->m_szSOAPPacket = (string) $sbString;
        $this->m_boPacketBuilt = true;
    }

    /**
     * @return bool
     */
    public function sendRequest()
    {
        $szString = '';
        $boReturnValue = false;
        $szUserAgent = 'ThePaymentGateway SOAP Library PHP';

        if (!$this->m_boPacketBuilt) {
            $this->buildPacket();
        }

        $this->m_xmlParser = null;
        $this->m_xmlTag = null;

        try {
            //intialising the curl for XML parsing
            $cURL = curl_init();

            //http settings
            $HttpHeader[] = 'SOAPAction:'.$this->getActionURI();
            $HttpHeader[] = 'Content-Type: text/xml; charset = utf-8';
            $HttpHeader[] = 'Connection: close';

            /*$http_options = array(CURLOPT_HEADER          => false,
            CURLOPT_HTTPHEADER      => $HttpHeader,
            CURLOPT_POST            => true,
            CURLOPT_URL             => $this->getURL(),
            CURLOPT_USERAGENT       => $szUserAgent,
            CURLOPT_POSTFIELDS      => $this->getSOAPPacket(),
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "UTF-8",
            CURLOPT_SSL_VERIFYPEER  => false,   //disabling default peer SSL certificate verification
        );

        curl_setopt_array($cURL, $http_options);*/

            curl_setopt($cURL, CURLOPT_HEADER, false);
            curl_setopt($cURL, CURLOPT_HTTPHEADER, $HttpHeader);
            curl_setopt($cURL, CURLOPT_POST, true);
            curl_setopt($cURL, CURLOPT_URL, $this->getURL());
            curl_setopt($cURL, CURLOPT_USERAGENT, $szUserAgent);
            curl_setopt($cURL, CURLOPT_POSTFIELDS, $this->getSOAPPacket());
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURL, CURLOPT_ENCODING, 'UTF-8');
            curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, true);

            //check if a certificate list is specified in the php.ini file, otherwise use the bundled one
            $caInfoSetting = ini_get('curl.cainfo');
            if (empty($caInfoSetting)) {
                curl_setopt($cURL, CURLOPT_CAINFO, $this->getCertDir().'/cacert.pem');
            }

            if (null != $this->getTimeout()) {
                curl_setopt($cURL, CURLOPT_TIMEOUT, $this->getTimeout());
            }

            //$this->m_szLastResponse = curl_exec($cURL);
            $szString = curl_exec($cURL);
            $errorNo = curl_errno($cURL); //test
            $errorMsg = curl_error($cURL); //test
            $header = curl_getinfo($cURL); //test
            curl_close($cURL);
            //return ['$szString' => $szString];
            //HACK - certain versions of PHP seem to cause extra characters to be added to the HTTP Header so that curl doesn't strip the header
            //the regex makes sure that only XML is passed through to the parser
            $pattern = '(<\?xml.*</soap:Envelope>)';
            preg_match($pattern, $szString, $matches, PREG_OFFSET_CAPTURE);

            $szString = $matches[0][0];

            $this->m_szLastResponse = $szString;

            $szString = str_replace('<soap:Body>', ' ', $szString);
            $szString = str_replace('</soap:Body>', ' ', $szString);

            $this->m_xmlParser = new XmlParser();

            if (!$this->m_xmlParser->parseBuffer($szString)) {
                throw new Exception('Could not parse response string');
            } else {
                $szResponsePathString = $this->m_szMethod.'Response';

                $this->m_xmlTag = $this->m_xmlParser->getTag(
                    $szResponsePathString
                );

                if (null == $this->m_xmlTag) {
                    throw new Exception("Couldn't find SOAP response tag: ".$szResponsePathString);
                }
                $boReturnValue = true;
            }

            $boReturnValue = true;
        } catch (Exception $exc) {
            $boReturnValue = false;
            $m_eLastException = $exc;
            //log error to the system log file
            error_log(
                "PHP Exception '".
                    $exc->getMessage().
                    "' at line ".
                    $exc->getLine().
                    " in '".
                    $exc->getFile().
                    "'. Stack trace: ".
                    $exc->getTraceAsString()
            );
        }

        return $boReturnValue;
    }

    /**
     * @param string                 $szName
     * @param string                 $szValue
     * @param SOAPParamAttributeList $lspaSOAPParamAttributeList
     */
    public function addParam(
        $szName,
        $szValue,
        SOAPParamAttributeList $lspaSOAPParamAttributeList = null
    ) {
        $spSOAPParam = null;

        $spSOAPParam = new SOAPParameter(
            $szName,
            $szValue,
            $lspaSOAPParamAttributeList
        );

        $this->addParam2($spSOAPParam, true);
    }

    /**
     * @param bool $boOverWriteValue
     *
     * @throws Exception
     */
    private function addParam2(SOAPParameter $spSOAPParam, $boOverWriteValue)
    {
        $lszHierarchicalNames = null;
        $nCurrentIndex = 0;
        $szTagNameToFind = null;
        $szString = null;
        $nCount = 0;
        $nCount2 = 0;
        $lspParamList = null;
        $spWorkingSOAPParam = null;
        $spNewSOAPParam = null;
        $boFound = false;
        $lspaAttributeList = null;
        $spaAttribute = null;
        $spaNewAttribute = null;
        $spaSOAPParamAttributeList = null;

        // need to check the name of the incoming item to see if it is a
        // complex soap parameter
        $lszHierarchicalNames = new StringList();

        $lszHierarchicalNames = SharedFunctions::getStringListFromCharSeparatedString(
            $spSOAPParam->getName(),
            '.'
        );

        if (1 == $lszHierarchicalNames->getCount()) {
            $this->m_lspSOAPParamList->add($spSOAPParam);
        } else {
            $lspParamList = $this->m_lspSOAPParamList;

            //complex
            for ($nCount = 0; $nCount < $lszHierarchicalNames->getCount(); ++$nCount
            ) {
                // get the current tag name
                $szString = (string) $lszHierarchicalNames->getAt($nCount);
                //continuework
                $szTagNameToFind = SharedFunctions::getArrayNameAndIndex(
                    $szString,
                    $nCurrentIndex
                );

                // first thing is to try to find the tag in the list
                if ($boFound || 0 == $nCount) {
                    // try to find this tag name in the list
                    $spWorkingSOAPParam = $lspParamList->isSOAPParamInList(
                        $szTagNameToFind,
                        $nCurrentIndex
                    );

                    if (null == $spWorkingSOAPParam) {
                        $boFound = false;
                    } else {
                        $boFound = true;

                        // is this the last item in the hierarchy?
                        if ($nCount == $lszHierarchicalNames->getCount() - 1) {
                            if ($boOverWriteValue) {
                                // change the value
                                $spWorkingSOAPParam->setValue(
                                    $spSOAPParam->getValue()
                                );
                            }

                            // add the attributes to the list
                            for ($nCount2 = 0; $nCount2 <
                                $spSOAPParam
                                    ->getSOAPParamAttributeList()
                                    ->getCount(); ++$nCount2
                            ) {
                                //$spaAttribute = $spaSOAPParamAttributeList[$nCount2];
                                $spaAttribute = $spSOAPParam
                                    ->getSOAPParamAttributeList()
                                    ->getAt($nCount2);

                                if (null != $spaAttribute) {
                                    $spaNewAttribute = new SOAPParamAttribute(
                                        $spaAttribute->getName(),
                                        $spaAttribute->getValue()
                                    );

                                    $spWorkingSOAPParam
                                        ->getSOAPParamAttributeList()
                                        ->add($spaNewAttribute);
                                }
                            }
                        }
                        $lspParamList = $spWorkingSOAPParam->getSOAPParamList();
                    }
                }

                if (!$boFound) {
                    // is this the last tag?
                    if ($nCount == $lszHierarchicalNames->getCount() - 1) {
                        $lspaAttributeList = new SOAPParamAttributeList();

                        for ($nCount2 = 0; $nCount2 <
                            $spSOAPParam
                                ->getSOAPParamAttributeList()
                                ->getCount(); ++$nCount2
                        ) {
                            $spaSOAPParamAttributeList = $spSOAPParam->getSOAPParamAttributeList();

                            $spaAttribute = $spaSOAPParamAttributeList->getAt(
                                $nCount2
                            );

                            if (null != $spaAttribute) {
                                $spaNewAttribute = new SOAPParamAttribute(
                                    $spaAttribute->getName(),
                                    $spaAttribute->getValue()
                                );
                                $lspaAttributeList->add($spaNewAttribute);
                            }
                        }

                        $spNewSOAPParam = new SOAPParameter(
                            $szTagNameToFind,
                            $spSOAPParam->getValue(),
                            $lspaAttributeList
                        );

                        $lspParamList->add($spNewSOAPParam);
                    } else {
                        $spNewSOAPParam = new SOAPParameter(
                            $szTagNameToFind,
                            '',
                            null
                        );
                        $lspParamList->add($spNewSOAPParam);
                        $lspParamList = $spNewSOAPParam->getSOAPParamList();
                    }
                }
            }
        }

        $this->m_boPacketBuilt = false;
    }

    /**
     * @param string $szName
     * @param string $szParamAttributeName
     * @param string $szParamAttributeValue
     *
     * @throws Exception
     */
    public function addParamAttribute(
        $szName,
        $szParamAttributeName,
        $szParamAttributeValue
    ) {
        $spSOAPParam = null;
        $lspaSOAPParamAttributeList = null;
        $spaSOAPParamAttribute = null;

        if (!is_string($szName) ||
            !is_string($szParamAttributeName) ||
            !is_string($szParamAttributeValue)
        ) {
            throw new \Exception('Invalid parameter type here');
        }

        $lspaSOAPParamAttributeList = new SOAPParamAttributeList();
        $spaSOAPParamAttribute = new SOAPParamAttribute(
            $szParamAttributeName,
            $szParamAttributeValue
        );
        $lspaSOAPParamAttributeList->add($spaSOAPParamAttribute);

        $spSOAPParam = new SOAPParameter(
            $szName,
            '',
            $lspaSOAPParamAttributeList
        );

        $this->addParam2($spSOAPParam, false);
    }

    //overloading constructors

    /**
     * @param string $szMethod
     * @param string $szMethodURI
     */
    private function SOAP1($szMethod, $szMethodURI, $certDir)
    {
        $this->SOAP3(
            $szMethod,
            $szMethodURI,
            null,
            'http://schemas.xmlsoap.org/soap/encoding/',
            true,
            null,
            $certDir
        );
    }

    /**
     * @param string $szMethod
     * @param string $szMethodURI
     * @param string $szURL
     */
    private function SOAP2($szMethod, $szMethodURI, $szURL, $certDir)
    {
        $this->SOAP3(
            $szMethod,
            $szMethodURI,
            $szURL,
            'http://schemas.xmlsoap.org/soap/encoding/',
            true,
            null,
            $certDir
        );
    }

    /**
     * @param string            $szMethod
     * @param string            $szMethodURI
     * @param string            $szURL
     * @param string            $szSOAPEncoding
     * @param bool              $boAddDefaultNamespaces
     * @param SOAPNamespaceList $lsnSOAPNamespaceList
     *
     * @throws Exception
     */
    private function SOAP3(
        $szMethod,
        $szMethodURI,
        $szURL,
        $szSOAPEncoding,
        $boAddDefaultNamespaces,
        SOAPNamespaceList $lsnSOAPNamespaceList = null,
        $certDir
    ) {
        $snSOAPNamespace = null;
        $nCount = 0;

        $this->m_szMethod = $szMethod;
        $this->m_szMethodURI = $szMethodURI;
        $this->m_szURL = $szURL;
        $this->m_szSOAPEncoding = $szSOAPEncoding;
        $this->certDir = $certDir;

        if ('' != $this->m_szMethodURI && '' != $this->m_szMethod) {
            if ('/' == $this->m_szMethodURI[strlen($this->m_szMethodURI) - 1]) {
                $this->m_szActionURI = $this->m_szMethodURI.$this->m_szMethod;
            } else {
                $this->m_szActionURI =
                    $this->m_szMethodURI.'/'.$this->m_szMethod;
            }
        }

        $this->m_lsnSOAPNamespaceList = new SOAPNamespaceList();

        if ($boAddDefaultNamespaces) {
            $snSOAPNamespace = new SOAPNamespace(
                'soap',
                'http://schemas.xmlsoap.org/soap/envelope/'
            );
            $this->m_lsnSOAPNamespaceList->add($snSOAPNamespace);
            $snSOAPNamespace = new SOAPNamespace(
                'xsi',
                'http://www.w3.org/2001/XMLSchema-instance'
            );
            $this->m_lsnSOAPNamespaceList->add($snSOAPNamespace);
            $snSOAPNamespace = new SOAPNamespace(
                'xsd',
                'http://www.w3.org/2001/XMLSchema'
            );
            $this->m_lsnSOAPNamespaceList->add($snSOAPNamespace);
        }
        if (null != $lsnSOAPNamespaceList) {
            for ($nCount = 0; $nCount < count($lsnSOAPNamespaceList); ++$nCount
            ) {
                $snSOAPNamespace = new SOAPNamespace(
                    $lsnSOAPNamespaceList->getAt($nCount)->getPrefix(),
                    $lsnSOAPNamespaceList->getAt($nCount)->getNamespace()
                );
                $this->m_lsnSOAPNamespaceList->add($snSOAPNamespace);
            }
        }
        $this->m_lspSOAPParamList = new SOAPParamList();

        $this->m_boPacketBuilt = false;
    }

    //constructor
    public function __construct()
    {
        $num_args = func_num_args();
        $args = func_get_args();

        switch ($num_args) {
            case 3:
                $this->SOAP1($args[0], $args[1], $args[2]);
                break;
            case 4:
                $this->SOAP2($args[0], $args[1], $args[2], $args[3]);
                break;
            case 7:
                $this->SOAP3(
                    $args[0],
                    $args[1],
                    $args[2],
                    $args[3],
                    $args[4],
                    $args[5],
                    $args[6]
                );
                break;
            default:
                throw new Exception('Invalid number of parameters for constructor SOAP');
        }
    }
}
