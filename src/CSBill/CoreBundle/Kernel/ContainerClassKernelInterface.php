<?php

namespace CSBill\CoreBundle\Kernel;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Interface KernelInterface.
 */
interface ContainerClassKernelInterface extends KernelInterface
{
    /**
     * Return the name of the cached container class.
     *
     * @return string
     */
    public function getContainerCacheClass();
}
