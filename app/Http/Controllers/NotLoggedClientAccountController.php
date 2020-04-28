<?php

namespace App\Http\Controllers;

use Client\ {
    Application\Service\ClientActivate,
    Application\Service\ClientGenerateActivationCode,
    Application\Service\ClientGenerateResetPasswordCode,
    Application\Service\ClientResetPassword,
    Domain\Event\ClientActivationCodeGenerated,
    Domain\Event\ClientPasswordResetCodeGenerated,
    Domain\Model\Client
};
use Resources\Application\Event\Dispatcher;

class NotLoggedClientAccountController extends Controller
{

    public function generateActivationCode()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $dispatcher = new Dispatcher();
        $listener = SendMailListenerBuilder::build();
        $dispatcher->addListener(ClientActivationCodeGenerated::EVENT_NAME, $listener);

        $service = new ClientGenerateActivationCode($clientRepository, $dispatcher);
        $email = $this->stripTagsInputRequest('email');
        $service->execute($email);

        return $this->commandOkResponse();
    }

    public function generateResetPasswordCode()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $dispatcher = new Dispatcher();
        $listener = SendMailListenerBuilder::build();
        $dispatcher->addListener(ClientPasswordResetCodeGenerated::EVENT_NAME, $listener);
        $service = new ClientGenerateResetPasswordCode($clientRepository, $dispatcher);

        $email = $this->stripTagsInputRequest('email');
        $service->execute($email);
        return $this->commandOkResponse();
    }

    public function activate()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $service = new ClientActivate($clientRepository);

        $email = $this->stripTagsInputRequest('email');
        $activationCode = $this->stripTagsInputRequest('activationCode');
        $service->execute($email, $activationCode);
        return $this->commandOkResponse();
    }

    public function resetPassword()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $service = new ClientResetPassword($clientRepository);

        $email = $this->stripTagsInputRequest('email');
        $resetPasswordCode = $this->stripTagsInputRequest('resetPasswordCode');
        $password = $this->stripTagsInputRequest('password');
        $service->execute($email, $resetPasswordCode, $password);

        return $this->commandOkResponse();
    }

}
