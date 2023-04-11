<?php
namespace GDW\Core\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use GDW\Core\Helper\Data as HelperData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AnySimpleFunction extends Command
{

    protected $state;
    protected $helperData;

    public function __construct(
        State $state,
        HelperData $helperData,
        $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->helperData = $helperData;
    }

    protected function configure()
    {
        $this->setName('gdw:run:function');
        $this->setDescription('Run any anonymous function when pass Class and Function');
        $this->addOption('class', 'c', InputOption::VALUE_REQUIRED, 'class');
        $this->addOption('function', 'f', InputOption::VALUE_REQUIRED, 'function');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       
        try {
            /* Neccesary in some task */
            $this->state->setAreaCode(Area::AREA_FRONTEND);

            $path = $input->getOption('class');
            $function = $input->getOption('function');

            if($path && $function){
                $output->write('Processing tasks... ');
                $startTime = microtime(true);
            
                $task = $this->helperData->getObject($path)->{$function}();
                
                $resultTime = microtime(true) - $startTime;

                /* Process */
                $output->writeln('');
                $output->writeln('<info>Run Function</info>');
                $output->writeln($task);
                $output->writeln('<info>Finish process in ' . gmdate('H:i:s', intval($resultTime)) . '.</info>');

            }else{
                $output->writeln('<error>A parameter is missing. --class or --function </error>');
            }

        } catch (\Exception $e) {
            $output->writeln('<error>An error encountered.</error>');
            throw $e;
        }
        return 0; /*Prevent message deprecated*/
    }
}