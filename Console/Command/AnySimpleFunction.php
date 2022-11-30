<?php
namespace GDW\Core\Console\Command;

use GDW\Core\Helper\Data;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AnySimpleFunction extends Command
{

    protected $state;
    protected $gdwHelper;

    public function __construct(
        State $state,
        Data $gdwHelper,
        $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->gdwHelper = $gdwHelper;
    }

    protected function configure()
    {
        $this->setName('gdw:anysimplefunction');
        $this->setDescription('Run Any function when pass Path and Function');
        $this->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'path');
        $this->addOption('function', 'f', InputOption::VALUE_REQUIRED, 'function');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       
        try {
            /* Neccesary in some task */
            $this->state->setAreaCode(Area::AREA_FRONTEND);

            $path = $input->getOption('path');
            $function = $input->getOption('function');

            if($path && $function){
                $output->write('Processing tasks... ');
                $startTime = microtime(true);
            
                $task = $this->gdwHelper->getObject($path)->{$function}();
                
                $resultTime = microtime(true) - $startTime;

                /* Process */
                $output->writeln('');
                $output->writeln('<info>Run Function</info>');
                $output->writeln($task);
                $output->writeln('<info>Finish process in ' . gmdate('H:i:s', $resultTime) . '.</info>');

            }else{
                $output->writeln('<error>A parameter is missing. --path or --function </error>');
            }

        } catch (\Exception $e) {
            $output->writeln('<error>An error encountered.</error>');
            throw $e;
        }
    }
}