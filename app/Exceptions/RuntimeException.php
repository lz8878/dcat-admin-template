<?php

namespace App\Exceptions;

class RuntimeException extends \RuntimeException
{
    /**
     * HTTP 响应状态码
     *
     * @var int
     */
    protected $status = 400;

    /**
     * 设置 HTTP 响应状态码
     *
     * @param  int  $status
     * @return $this
     */
    public function withStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * 获取 HTTP 响应状态码
     *
     * @return int
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * 报告异常
     *
     * @return bool
     */
    public function report(): bool
    {
        return true;
    }
}
