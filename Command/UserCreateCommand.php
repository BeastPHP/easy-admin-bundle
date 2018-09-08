<?php

namespace Beast\EasyAdminBundle\Command;

use Beast\EasyAdminBundle\Entity\Authorization\Administrator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use \Exception;

class UserCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('authorization:user:create')
            ->setAliases(array('admin:create'))
            ->setDescription('create the system user');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $username = new Question('Please enter the username:');
        $password = new Question('Please enter the password:');

        $username = $helper->ask($input, $output, $username);
        $password = $helper->ask($input, $output, $password);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $this->getContainer()->get('doctrine')->getRepository(Administrator::class);

        try {

            $administrator = $repository->findOneByUsername($username);

            if ($administrator) {
                throw new Exception("用户名已经存在");
            }

            $administrator = new Administrator;
            $administrator->setUsername($username);
            $administrator->setPassword($password);
            $administrator->setIsActive(1);
            $administrator->setIsSuperAdmin(1);
            $administrator->setLastLogin(new \DateTime());
            $em->persist($administrator);
            $em->flush();

            $text = $username . '|' . $password . ' 创建成功.';
        } catch (Exception $e) {
            $text = $username . '|' . $password . " 创建失败. \r\n\r\n" . $e->getMessage();
        }

        $output->writeln($text);
    }
}
