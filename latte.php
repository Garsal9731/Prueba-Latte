<?php

    // activa Tracy
    Tracy\Debugger::enable();

    $latte = new Latte\Engine;
    // activa la extensión de Tracy
    $latte->addExtension(new Latte\Bridges\Tracy\TracyExtension);


    // directorio caché
    $latte->setTempDirectory('./cachelatte/');
        
    // Creamos la plantilla
    class MailTemplateParameters{
        public function __construct(
            public string $lang,
            public Address $address,
            public string $subject,
            public array $items,
            public ?float $price = null,
        ){}

        public function getPrice(){
            return $this->precio;
        }
    }

    // Aplicamos filtros, funciones y valores por defecto a la plantilla
    $latte->render('mail.latte', new MailTemplateParameters(
        lang: $this->lang,
        subject: $title,
        price: $this->getPrice(),
        items: [],
        address: $userAddress,
    ));

    // ----------------------------------------------------------------------------------------

    // Le damos nuestra ruta de php.ini
    // ! Las plantillas no aceptan código nativo de php, solo objetos o matrices
    // ! Latte solo es funcional en php 8.0-8.2 
    $latte->enablePhpLinter('/etc/php/8.1/cli/php.ini');

    // Probamos a recopilar un archivo y si genera un error lo mostrará usando un objeto nativo
    try{
        $latte->compile('mail.latte');
    }catch(Latte\CompileException $e){
        // atrapa errores Latte y también Compile Error en PHP
        echo 'Error: ' . $e->getMessage();
    }


    // ----------------------------------------------------------------------------------------

    // ? Puede traducir código de otros lenguajes
    class MyTranslator{
        public function __construct(private string $lang){}

        public function translate(string $original): string{
            // crear $traducido a partir de $original según $this->lang
            return $translated;
        }
    }

    $translator = new MyTranslator($lang);

    // Comprueba y traduce el idioma y lo añade como una extensión
    $extension = new Latte\Essential\TranslatorExtension(
        $translator->translate(...), // [$translator, 'translate'] en PHP 8.0
    );
    $latte->addExtension($extension);

    // ----------------------------------------------------------------------------------------

    // ! Si se genera un archivo en html (por ejemplo) con string se debe crear un getter para evitar vulnerabilidades de XSS
    // Le indicamos donde está el stringloader (Para imprimir por pantalla)
    $latte->setLoader(new Latte\Loaders\StringLoader([
        'main.file' => '{include other.file}',
        'other.file' => '{if true} {$var} {/if}',
    ]));
    
    // Le decimos que lo renderice con unos parametros opcionales (funciona como un getter)
    $latte->render('main.file', $params);

?>