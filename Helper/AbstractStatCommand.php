<?php

namespace App\StatBundle\Helper;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractStatCommand extends ContainerAwareCommand implements InterfaceStatCommand {

    protected $lockFileName = 'statlock.txt';
    protected $lockFilePath;
    protected $methods = array();
    protected $input;
    protected $output;
    protected $container;
    protected $doctrine;
    protected $defaultManager;
    protected $defaultManagerConn;
    protected $progress;
    protected $method;

    protected function configure() {
        $this
                ->setName('app:stat')
                ->setDescription('generate app statistics')
                ->addOption('method', null, InputOption::VALUE_OPTIONAL, 'statMethodGenerate')
        ;
    }

    // ~

    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->input = $input;
        $this->output = $output;
        $this->container = $this->getContainer();
        $this->doctrine = $this->container->get('doctrine');
        $this->defaultManager = $this->doctrine->getManager();
        $this->defaultManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->defaultManagerConn = $this->defaultManager->getConnection();
        $this->defaultManagerConn->getConfiguration()->setSQLLogger(null);
        $this->method = $input->getOption('method');
        $this->lockFilePath = $this->container->get('kernel')->getLogDir() . '/' . $this->lockFileName;

        try {
            file_get_contents($this->lockFilePath);
            $this->output->writeln('app:stat already running - exit');
            exit();
        } catch (\Exception $ex) {
            \file_put_contents($this->lockFilePath, $this->lockFileName);
        }



        foreach (get_class_methods($this) as $class_method) {
            if (strpos($class_method, 'stat') !== false && strpos($class_method, 'Generate') !== false) {
                $this->methods[$class_method] = $class_method;
            }
        }

        /**
         * if option method is null then run all statMethodNameGenerate functions
         */
        try {
            $this->fetchMethods($this->method);
        } catch (\Exception $exc) {
            $this->output->writeln('Probably method not isset in your stat comand ' . get_class($this) . ':' . $this->method . ' see error code');
            $this->output->writeln($exc->getCode());
            $this->output->writeln($exc->getMessage());
        }

        /**
         * save all stats in db
         */
        $this->defaultManager->flush();

        try {
            unlink($this->lockFilePath);
        } catch (\Exception $ex) {
            
        }
    }

    /**
      @example
      public function fetchMethods($methodName = null) {
      if ($methodName === null) {
      foreach ($this->methods as $method) {
      call_user_func(array(get_class(), $method));
      }
      } else {
      call_user_func(array(get_class(), $methodName));
      }
      }
     */
    abstract public function fetchMethods($methodName = null);
}
