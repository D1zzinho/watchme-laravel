<?php

namespace App\Helpers;

use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\NoReturn;

class VideoStream
{
    private string $path;
    private Stream $stream;
    private int $buffer = 1024 * 5;
    private int $length;
    private int $size;
    private string $mime;

    public function __construct($filePath = '')
    {
        $this->path = $filePath;
        $this->mime = Storage::mimeType($filePath);
    }

    /**
     * Open stream.
     */
    private function open(): void
    {
        if (!($stream = Storage::readStream($this->path))) {
            die('Could not open stream for reading');
        }

        $this->stream = new Stream($stream);
    }

    /**
     * Set proper header to serve the video content.
     */
    private function setHeader(): void
    {
        ob_get_clean();
        $this->size = Storage::size($this->path);

        header("Content-Type: {$this->mime}");
        header("Accept-Ranges: bytes");

        if (false !== $range = Request::server('HTTP_RANGE', false)) {
            list($param, $range) = explode('=', $range);

            if (strtolower(trim($param)) !== 'bytes') {
                header('HTTP/1.1 400 Bad Request');
                exit;
            }

            list($from, $to) = explode('-', $range);

            if ($from === '') {
                $end = $this->size - 1;
                $start = $end - intval($from);
            } elseif ($to === '') {
                $start = intval($from);
                $end = $this->size - 1;
            } else {
                $start = intval($from);
                $end = intval($to);
            }

            if ($end >= $this->size) {
                $end = $this->size - 1;
            }

            $this->length = $end - $start + 1;

            $this->stream->seek($start);
            header('HTTP/1.1 206 Partial Content');
            header("Content-Range: bytes {$start}-{$end}/{$this->size}");
            header("Content-Length: {$this->length}");
        } else {
            header("Content-Length: {$this->size}");
            $this->length = $this->size;
        }
    }

    /**
     * Finish currently opened stream.
     */
    #[NoReturn] private function end(): void
    {
        $this->stream->close();
        exit;
    }

    /**
     * Performs the streaming of calculated range.
     **/
    private function stream(): void
    {
        $remainingBytes = $this->length ?? $this->size;
        while (!$this->stream->eof() && $remainingBytes > 0) {
            $bytesToRead = $this->buffer;
            echo $this->stream->read($bytesToRead);
            $remainingBytes -= $bytesToRead;
            flush();
        }
    }

    /**
     * Start streaming.
     */
    #[NoReturn] function start(): void
    {
        $this->open();
        $this->setHeader();
        $this->stream();
        $this->end();
    }
}
