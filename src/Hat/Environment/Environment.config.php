<?php
namespace Hat\Environment;

use Hat\Environment\Kit\Kit;
use Hat\Environment\Kit\Service;
use Hat\Environment\Kit\Factory;

return array(


    // options
    'default.profile.name' => 'environment.ini',


    //services

    'request.handler' => new Service(function (Kit $kit) {

        $handler = new \Hat\Environment\Handler\RequestHandler();

        $handler->addHandler(new \Hat\Environment\Handler\Request\HelpHandler());
        $handler->addHandler(new \Hat\Environment\Handler\Request\RequireProfileHandler());

        $handler->addHandler(new \Hat\Environment\Handler\Request\HandleProfileHandler(
            $kit->get('profile.handler')
        ));

        return $handler;
    }),


    'profile.handler' => new Service(function (Kit $kit) {

        $handler = new \Hat\Environment\Handler\ProfileHandler(
            $kit->get('profile.loader'),
            $kit->get('profile.register'),
            $kit->get('definition.handler'),
            $kit->get('output')
        );

        return $handler;
    }),


    'definition.handler' => new Service(function (Kit $kit) {

        $handler = new \Hat\Environment\Handler\DefinitionHandler();

        // validation and checks
        $handler->addHandler(new \Hat\Environment\Handler\Definition\ValidateHandler());
        $handler->addHandler(new \Hat\Environment\Handler\Definition\DependsHandler($kit));
        $handler->addHandler(new \Hat\Environment\Handler\Definition\RecompileHandler());

        // execute command
        $handler->addHandler(new \Hat\Environment\Handler\Definition\ExecuteCommandHandler($kit));

        // handle status
        $handler->addHandler(new \Hat\Environment\Handler\Definition\NegativeHandler());
        $handler->addHandler(new \Hat\Environment\Handler\Definition\StatusHandler());

        // makes some output
        $handler->addHandler(new \Hat\Environment\Handler\Definition\StatusLineOutputHandler($kit->get('output')));

        // handle on status
        $handler->addHandler(new \Hat\Environment\Handler\Definition\OnPassHandler($kit));
        $handler->addHandler(new \Hat\Environment\Handler\Definition\OnFailHandler($kit));

        // re execute if fixed
        $handler->addHandler(new \Hat\Environment\Handler\Definition\ReExecuteCommandHandler($kit));

        // makes result output
        $handler->addHandler(new \Hat\Environment\Handler\Definition\ResultOutputHandler($kit->get('output')));

        // makes some docs
        $handler->addHandler(new \Hat\Environment\Handler\Definition\DocHandler($kit));

        return $handler;
    }),

    'profile.loader' => new Service(function (Kit $kit) {

        $loader = new \Hat\Environment\Loader\ProfileLoader(
            $kit->get('profile.load.handler'),
            $kit->get('output')
        );

        return $loader;
    }),

    'profile.load.handler' => new Service(function (Kit $kit) {

        $handler = new \Hat\Environment\Handler\Profile\ProfileLoadHandler();

        $handler->addHandler(new \Hat\Environment\Handler\Profile\ProfileExtendsHandler($kit));
        $handler->addHandler(new \Hat\Environment\Handler\Profile\DefinitionExtendsHandler());

//        $handler->addHandler(new \Hat\Environment\Handler\Profile\ProfileParentFinderHandler($kit));
//        $handler->addHandler(new \Hat\Environment\Handler\Profile\ProfileGlobalHandler());

        return $handler;
    }),

    'profile.register' => new Service(function (Kit $kit) {
        return new \Hat\Environment\Register\ProfileRegister();
    }),

    'output' => new Service(function (Kit $kit) {
        $output = new \Hat\Environment\Output\EnvironmentOutput($kit->get('request'));
        return $output;
    }),

    'request' => new Service(function (Kit $kit) {

        return new \Hat\Environment\Request\CliRequest(
            array(
                'profile' => $kit->get('default.profile.name')
            )
        );

    }),


    // end services


);
