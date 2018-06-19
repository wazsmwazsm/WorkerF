<?php
namespace WorkerF\Http;

/**
 * HTTP requests File.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
Class File
{
    /**
     * file name.
     *
     * @var string
     */
    protected $_fileName;
    
    /**
     * file date.
     *
     * @var binary
     */
    protected $_fileData;

    /**
     * file size.
     *
     * @var int
     */
    protected $_fileSize;

    /**
     * file type.
     *
     * @var string
     */
    protected $_fileType;

    /**
     * create request file info from file data array.
     *
     * @param array $file
     */
    public function __construct(array $file)
    {
        $this->_fileName = $file['file_name'];
        $this->_fileData = $file['file_data'];
        $this->_fileSize = $file['file_size'];
        $this->_fileType = $file['file_type'];
    }

    /**
     * get file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->_fileName;
    }

    /**
     * get file size.
     *
     * @return int
     */
    public function getFileSize()
    {
        return $this->_fileSize;
    }
    
    /**
     * get file type.
     *
     * @return string
     */
    public function getFileType()
    {
        return $this->_fileType;
    }

    /**
     * move file from tmp dir to target dir.
     *
     * @param string $targetPath
     * @param string $targetName
     * @return mixed
     */
    public function move($targetPath, $targetName)
    {
        $target = rtrim($targetPath, '/').'/'.$targetName;
        return file_put_contents($target, $this->_fileData);
    }

}
