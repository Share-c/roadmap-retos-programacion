<?php
/**
 * @author Share-c
 * @link https://github.com/Share-c
 */



/**
 * @see http://en.wikipedia.org/wiki/ANSI_escape_code
 */
abstract class AbstractOutput
{
    /**
     * Lista de 'alias' a su equivalente ANSI expresado como un int.
     * 
     * @var array<string, int>
     */
    private static array $ansiMap = [
        // Reset / Valor normal. Todos los atributos desactivados.
        'reset'         => 0,
        // Negrita / 
        'bold'          => 1,
        // Suavizada
        'faint'         => 2,
        // Itálica
        'italic'        => 3,
        // Subrayado
        'underline'     => 4,
        // Parpadeo
        'blink'         => 5,
        // Parpadeo rápido
        'blinkfast'     => 6,
        'negative'      => 7,
        'conceal'       => 8,
        'crossed'       => 9,
        'font/default'  => 10,
        'font/0'        => 10,
        'font/1'        => 11,
        'font/2'        => 12,
        'font/3'        => 13,
        'font/4'        => 14,
        'font/5'        => 15,
        'font/6'        => 16,
        'font/7'        => 17,
        'font/8'        => 18,
        'font/9'        => 19,
        'fraktur'       => 20,
        'dblunderline'  => 21,
        '!bold'         => 22,
        '!italic'       => 23,
        '!underline'    => 24,
        '!blink'        => 25,
        'positive'      => 27,
        'reveal'        => 28,
        '!crossed'      => 29,
        'text/black'    => 30,
        'text/red'      => 31,
        'text/green'    => 32,
        'text/yellow'   => 33,
        'text/blue'     => 34,
        'text/magenta'  => 35,
        'text/cyan'     => 36,
        'text/white'    => 37,
        'text/default'  => 39,
        'bg/black'      => 40,
        'bg/red'        => 41,
        'bg/green'      => 42,
        'bg/yellow'     => 43,
        'bg/blue'       => 44,
        'bg/magenta'    => 45,
        'bg/cyan'       => 46,
        'bg/white'      => 47,
        'bg/default'    => 49,
        'frame'         => 51,
        'encircle'      => 52,
        'overline'      => 53,
        '!encircle'     => 54,
        '!frame'        => 54,
        '!overline'     => 55,
    ];

    /**
     * Método para obtener un texto formateado en ANSI con el fin de estilo marcado.
     * 
     * @param string|array $formato El formato que desa darse al texto.
     * @param string $texto Texto que se desea formatear.
     * 
     * @return string Bloque formateado marcando el fin del estilo
     */
    protected static function bloque(string|array $formato = 'reset', ?string $texto = null): string
    {
        return self::escapar($formato, $texto) . "\033[0m";
    }

    /**
     * Método para obtener un texto formateado en ANSI
     * 
     * @param string|array $formato El formato que desa darse al texto.
     * @param string $texto Texto que se desea formatear.
     * 
     * @return string Texto formateado
     */
    protected static function escapar(string|array $formato = 'reset', ?string $texto = null): string
    {
        if (!is_array($formato)) {
            $formato = str_replace(',', ';', $formato);
            $formato = explode(';', $formato);
        }

        foreach ($formato as $f) {
            $escape[] = self::$ansiMap[trim($f)];
        }

        // Se resetea.
        if ($texto === null) {
            return "\033[0m";
        }

        return "\033[" . implode(';', $escape) . "m" . $texto;
    }

    /**
     * Método para otorgar una pausa en la salida de la terminal
     * 
     * @param int $segundos Pausa en segundos
     */
    protected static function pausa(int $segundos)
    {
        sleep($segundos);
    }

    /**
     * Método para simular un efecto de macanografía en la salida de terminal.
     * 
     * @param string $texto
     * @param int $pausa Pause entre letra en microsegundos (1 segundo = 1000000 microsegundos)
     */
    protected static function mecanografía($texto, $pausa = 100000)
    {
        $caracteres = str_split($texto);
        foreach ($caracteres as $caracter) {
            echo $caracter;
            usleep($pausa); // En microsegundos
        }
    }
}


class Output extends AbstractOutput
{
    //private int $ronda;

    private array $orden = [
        1   => 'final',
        2   => 'semifinales',
        3   => 'cuartos',
        4   => 'octavos'
    ];

    private array $ordinal = [
        1   => 'primer',
        2   => 'segundo',
        3   => 'tercer',
        4   => 'cuarto',
        5   => 'quinto',
        6   => 'sexto',
        7   => 'septimo',
        8   => 'octavo'
    ];

    public function inicioEjecutarTorneo()
    {
        $txt =  PHP_EOL . "Bienvenidos al " . Config::BASE['nombreTorneo'] . PHP_EOL;

        echo parent::bloque('bg/green, text/red, bold', $txt);
    }

    public function sorteoEjecutarTorneo(array $contenedor)
    {
        $txt = 'En este torneo participaran estos ' . count($contenedor) . ' luchadores: ' . PHP_EOL;
        foreach ($contenedor as $luchador) {
            $txt .= '*- ' . $luchador->nombre . PHP_EOL;
        }
        $txt = PHP_EOL . $txt . PHP_EOL;

        parent::mecanografía($txt);

        parent::pausa(1);
    }

    public function realizarRondaTorneo(array $ronda, string $luchador1, string $luchador2)
    {
        //echo "sabemos la ronda y el combate" . PHP_EOL;

        if ($ronda[0] == '1') {
            echo 'Se disputa la ' . $this->orden[1] . PHP_EOL;
        } else {
            echo 'Se disputa el ' . $this->ordinal[$ronda[1]] . ' combate de la ronda de ' . $this->orden[$ronda[0]] . PHP_EOL;
        }
        echo 'Se enfrentan ' . parent::bloque('text/white', $luchador1) . ' contra ' . parent::bloque('text/white', $luchador2) . PHP_EOL;

        parent::pausa(1);
    }

    public function ganadorRealizarRondaTorneo(array $ronda, string $ganador)
    {
        if ($ronda[0] != '1') {
            echo parent::bloque('text/green, blink, underline', $ganador) . " gana el combate." . PHP_EOL . PHP_EOL;
        } else {
            echo parent::bloque('text/red, dblunderline, blink', $ganador) . " es el nuevo campeón del mundo de artes marciales." . PHP_EOL . PHP_EOL;
        }

        parent::pausa(1);
    }

    public function quienEmpiezaBatalla(string $atacante)
    {
        if (Config::TORNEO['resumirBatalla']) {
            return;
        }
        echo $atacante . ' inicia el ataque.' . PHP_EOL;
    }

    public function esquivarRondaBatalla(string $defensor)
    {
        if (Config::TORNEO['resumirBatalla']) {
            return;
        }
        echo $defensor . ' logra esquiva el ataque.' . PHP_EOL;
    }

    public function rondaBatalla(string $defensor, int $daño, int $salud)
    {
        if (Config::TORNEO['resumirBatalla']) {
            return;
        }
        echo $defensor . ' recibe ' . $daño . ' puntos de daño. Le quedan: ' . $salud . ' puntos de vida.' . PHP_EOL;
    }
}

class Config
{
    const BASE = [
        'nombreTorneo'      => '42 Torneo de Bolas do Dragón', //string
        'valorSalud'        => 100, //int
        'maxAtributo'       => 100, //int
        'porcentEsquivar'   => 20,  //int
    ];

    /**
     * El primer valor siempre será escogido.
     */
    const PERSONAJES = [
        'Son Goku'      => [90, 85, 90],
        'Son Gohanda'   => [84, 84, 79],
        'Vexeta'        => [85, 90, 80],
        'Piccoro'       => [75, 75, 70],
        'Toranks'       => [80, 80, 85],
        'Son Goten'     => [86, 91, 79],
        'Krilin'        => [70, 69, 82],
        'Yamcha'        => [50, 50, 50],
        'Tensihan'      => [55, 55, 45],
        'Chaoz'         => [45, 50, 45],
        'Mutenroi'      => [60, 65, 60],
        'Yajirobe'      => [30, 30, 30],
        'Popo'          => [66, 66, 66],
        'Raditz'        => [75, 70, 75],
        'Nappa'         => [70, 65, 70],
        'Freezer'       => [80, 80, 95],
        'C-17'          => [65, 50, 55],
        'C-18'          => [66, 70, 75],
        'Celula'        => [68, 69, 71],
        'Tou Pai Pai'   => [45, 50, 40],
        'Sr. Satan'     => [20, 30, 20],
        'Videl'         => [55, 76, 68],
        'Pan'           => [69, 65, 90],
        'Ub'            => [95, 80, 80],
    ];

    const LUCHADOR = [
        // Valor de salud con la que comienza el combate.
        'valorSalud'    => self::BASE['valorSalud'], //int
        // Número máximo de letras en el nombre generado.
        'maxNombre'     => 7, //int
        // Número mínimo de letras en el nombre generado
        'minNombre'     => 4, //int
        // Número máximo de vocales consebutivas en el nombre generado
        'maxVocal'      => 3, //int
        // Número máximo de consonantes consecutivas en el nombre generado
        'maxConso'      => 2, //int
        // Lista de vocales disponibles
        'vocales'       => 'aeiou', //string
        // Lista de consonantes disponibles
        'consonantes'   => 'bcdfghjklmnprstvxz', //string
        // Valor máximo de los atributos
        'maxAtributo'   => self::BASE['maxAtributo'], //int
        // Valor mínimo de los atributos
        'minAtributo'   => 15, //int

    ];

    const TORNEO = [
        // Número de rondas que tendra el torneo.
        'numeroRondas'  => 4, //int # Max 4 . No tengo personajes para más.
        // Determinar si usamos nombres de PERSONAJES o aleatoreos
        'nombresReales' => true, //bool
        // Determinar si muestran las batallas en modo resumen.
        'resumirBatalla' => true, //bool
    ];
}


class Luchador
{
    /**
     * @var string Nombre del luchador
     */
    private string $nombre;

    /**
     * @var int Valor de la velocidad
     */
    private int $velocidad;

    /**
     * @var int Valor de la fuerza
     */
    private int $ataque;

    /**
     * @var int Valor de la defensa
     */
    private int $defensa;

    /**
     * @var int Valor de la salud.
     */
    public int $salud;

    /**
     * Método mágico __get() para poder acceder a las propiedades privadas.
     * Como son valores inmutables nos aseguramos el "solo lectura"
     * 
     * @param string $propiedad Nombre de una propiedad de esta clase.
     */
    public function __get($propiedad)
    {
        if (property_exists($this, $propiedad)) {
            return $this->$propiedad;
        }
    }

    /**
     * Dar el valor de salud predeterminado
     * al principio de cada combate.
     */
    public function regenerarSalud()
    {
        $this->salud = Config::LUCHADOR['valorSalud'];
    }

    /**
     * Método para crear un personaje real.
     * 
     * @param string @nombre Nombre del personaje
     * @param int $velocidad    La velocidad del personaje
     * @param int $ataque       El poder de ataque del personaje
     * @param int $defensa      Defensa del personaje
     * 
     * @return void static parameters
     */
    public function crearPersonajeReal(string $nombre, int $velocidad, int $ataque, int $defensa): void
    {
        $this->nombre = $nombre;
        $this->velocidad = $velocidad;
        $this->ataque = $ataque;
        $this->defensa = $defensa;
    }

    /**
     * Crea un personaje aleatoreo.
     * 
     * @return void static parameters
     */
    public function generarPersonajeAleatoreo(): void
    {
        $this->nombre = $this->crearNombre();
        $this->velocidad = $this->crearAtributo();
        $this->ataque = $this->crearAtributo();
        $this->defensa = $this->crearAtributo();
    }

    /**
     * @return string Un nombre aleatoreo
     */
    private function crearNombre(): string
    {
        // Verifica los valores correctos en la configuración.
        if (Config::LUCHADOR['minNombre'] > Config::LUCHADOR['maxNombre'] || Config::LUCHADOR['minNombre'] < 1) {
            die("La longitud del nombre está mal.");
        }

        $longitud = random_int(Config::LUCHADOR['minNombre'], Config::LUCHADOR['maxNombre']);

        // Variables.
        $vocales = Config::LUCHADOR['vocales'];
        $consonantes = Config::LUCHADOR['consonantes'];
        $string = '';
        $contadorConso = 0;
        $contadorVocal = 0;

        for ($i = 0; $i < $longitud; $i++) {
            if ($contadorVocal === Config::LUCHADOR['maxVocal']) {
                $string .= $consonantes[rand(0, strlen($consonantes) - 1)];
                $contadorConso++;
                $contadorVocal = 0;
            } else if ($contadorConso === Config::LUCHADOR['maxConso']) {
                $string .= $vocales[rand(0, strlen($vocales) - 1)];
                $contadorConso = 0;
                $contadorVocal++;
            } else {
                if (rand(0, 1) === 0) {
                    $string .= $vocales[rand(0, strlen($vocales) - 1)];
                    $contadorVocal++;
                } else {
                    $string .= $consonantes[rand(0, strlen($consonantes) - 1)];
                    $contadorConso++;
                }
            }
        }

        return ucfirst($string);
    }

    /**
     * @return int Atributo aleatoreo
     */
    private function crearAtributo()
    {
        return random_int(Config::LUCHADOR['minAtributo'], Config::LUCHADOR['maxAtributo']);
    }
}


class Batalla
{
    /**
     * @var Luchador
     */
    private Luchador $luchador1;

    /**
     * @var Luchador
     */
    private Luchador $luchador2;

    /**
     * @var Output
     */
    private Output $echo;

    /**
     * @var array<string, Luchador>
     */
    private array $rol = [
        'ataca'     => null,
        'defiende'  => null
    ];

    public function __construct(Luchador $luchador1, Luchador $luchador2)
    {
        // Output en el terminal
        $this->echo = new Output();

        $this->luchador1 = $luchador1;
        $this->luchador2 = $luchador2;

        // Reiniciar la salud de los luchadores.
        $this->reiniciarSalud();
        //Se determina quien empieza la batalla.
        $this->quienEmpieza();
    }

    /** 
     * Disputar el combate 
     * 
     * @return Luchador El que haya ganado el combate.
     */
    public function lucha(): Luchador
    {
        // Se ejecuta ronda() jasta tener un ganador.
        while (true) {
            $this->ronda();

            $ganador = $this->determinarGanador();
            if ($ganador) {


                return $ganador;
            }
        }
    }

    private function ronda(): void
    {
        // Comprobamos si esquiva el golpe.
        if ($this->esquivar(Config::BASE['porcentEsquivar'])) {
            $this->echo->esquivarRondaBatalla($this->rol['defiende']->nombre);
            $this->cambioRol();
            return;
        }

        // Lucha
        $daño = $this->rol['ataca']->ataque > $this->rol['defiende']->defensa
            ? $this->rol['ataca']->ataque - $this->rol['defiende']->defensa
            : round(0.1 * $this->rol['ataca']->ataque);
        $this->rol['defiende']->salud -= $daño;

        $this->echo->rondaBatalla($this->rol['defiende']->nombre, $daño, $this->rol['defiende']->salud);
        $this->cambioRol();
    }

    private function determinarGanador(): ?Luchador
    {
        if ($this->luchador1->salud <= 0) {
            return $this->luchador2;
        }

        if ($this->luchador2->salud <= 0) {
            return $this->luchador1;
        }

        return null;
    }

    /**
     * Se determina quien es el primero en atacar.
     * 
     * @return void $rol
     */
    private function quienEmpieza(): void
    {
        $velocidad1 = $this->luchador1->velocidad;
        $velocidad2 = $this->luchador2->velocidad;

        if ($velocidad1 > $velocidad2) {
            $this->rol['ataca'] = $this->luchador1;
            $this->rol['defiende'] = $this->luchador2;
        } else if ($velocidad1 < $velocidad2) {
            $this->rol['ataca'] = $this->luchador2;
            $this->rol['defiende'] = $this->luchador1;
        } else {
            // Si la velocidad es igual, se sortea.
            if (rand(0, 1) === 0) {
                $this->rol['ataca'] = $this->luchador1;
                $this->rol['defiende'] = $this->luchador2;
            } else {
                $this->rol['ataca'] = $this->luchador2;
                $this->rol['defiende'] = $this->luchador1;
            }
        }
        $this->echo->quienEmpiezaBatalla($this->rol['ataca']->nombre);
        return;
    }

    /**
     * Determina si esquiva el golpe.
     * 
     * @return bool Un $porcentaje de veces responde true.
     */
    private function esquivar(int $porcentaje): bool
    {
        return random_int(1, 100) <= $porcentaje;
    }

    /**
     * Cambia roles entre atacante y defensor.
     * 
     * @return void
     */
    private function cambioRol(): void
    {
        [$this->rol['ataca'], $this->rol['defiende']] = [$this->rol['defiende'], $this->rol['ataca']];
        return;
    }

    /**
     * Resetear los valores de salud de cada jugador.
     */
    private function reiniciarSalud(): void
    {
        $this->luchador1->regenerarSalud();
        $this->luchador2->regenerarSalud();
        return;
    }
}

class Torneo
{
    /**
     * Instancia de Output
     * 
     * @var Output
     */
    private Output $echo;

    /**
     * Contenedor para los luchadores
     * 
     * @var array<Luchador>
     */
    private array $contenedor;

    /**
     * Lista para almacenar la ronda y el combate
     * 
     * @var array<int,int>
     */
    private array $ronda = [0, 0];

    public function __construct()
    {
        // Instanciar Output
        $this->echo = new Output();
        // Genarar participantes
        $this->generarParticipantes();
        // Generar ronda
        $this->ronda[0] = log(count($this->contenedor), 2) + 1;
    }

    public function ejecutar()
    {
        $this->echo->inicioEjecutarTorneo();
        // Se sortea un orden aleatoreo
        $this->sorteo();
        $this->echo->sorteoEjecutarTorneo($this->contenedor);
        // Se hacen las rondas de combates
        $this->ronda();
    }

    /**
     * 
     */
    private function ronda()
    {
        while (count($this->contenedor) > 1) {
            $this->ronda[0] -= 1;
            $this->realizarRonda();
        }

        return $this->contenedor;
    }

    /**
     * @return void $contenedor
     */
    private function realizarRonda(): void
    {
        $ganador = [];
        $this->ronda[1] = 0;

        while (!empty($this->contenedor)) {
            $luchador1 = array_shift($this->contenedor); // Extrae el primer luchador.
            $luchador2 = array_shift($this->contenedor); // Extrae el segundo luchador.
            $this->ronda[1] += 1;
            $this->echo->realizarRondaTorneo($this->ronda, $luchador1->nombre, $luchador2->nombre);
            $batalla = new Batalla($luchador1, $luchador2);
            $ganador[] = $batalla->lucha();
            $this->echo->ganadorRealizarRondaTorneo($this->ronda, $ganador[array_key_last($ganador)]->nombre);
        }
        $this->contenedor = $ganador;
    }

    /**
     * 
     */
    private function sorteo()
    {
        shuffle($this->contenedor);
    }

    /**
     * Llena $contenedor con los participante.
     * 
     * @return $contenedor
     */
    private function generarParticipantes()
    {
        $numero = pow(2, Config::TORNEO['numeroRondas']);

        if (!$this->verificarNumeroParticipantes($numero)) {

            die('Número de participantes incorrecto.');
        }

        if (Config::TORNEO['nombresReales']) {

            $this->generarNombresReales($numero);
        } else {
            $this->generarNombresAleatoreos($numero);
        }
    }

    /**
     * Escoge nombres aleatoreos del array Config::PERSONAJES.
     * El primer valor siempre se escoge.s
     * 
     * @param int $numero Numero de nombres a generar.
     * 
     * @return void $contenedor
     */
    private function generarNombresReales(int $numero): void
    {
        $personajes = Config::PERSONAJES;

        // Verificar
        if (count($personajes) < $numero) {
            die('Número incorrecto de participantes.');
        }

        // Se carga siempre el primer luchador >> 'Son Goku'
        $clave = array_key_first($personajes);
        $luchador = new Luchador();
        $luchador->crearPersonajeReal($clave, $personajes[$clave][0], $personajes[$clave][1], $personajes[$clave][2]);
        $this->contenedor[] = $luchador;
        //Sacar el elemento del array.
        unset($personajes[$clave]);

        // Resto de personajes
        $clavesResto = array_rand($personajes, $numero - 1);
        $resto = [];
        if ($numero > 2) {
            foreach ($clavesResto as $c) {
                $luchador = new Luchador();
                $luchador->crearPersonajeReal($c, $personajes[$c][0], $personajes[$c][1], $personajes[$c][2]);
                $this->contenedor[] = $luchador;
            }
        } else {
            $luchador = new Luchador();
            $luchador->crearPersonajeReal($clavesResto, $personajes[$clavesResto][0], $personajes[$clavesResto][1], $personajes[$clavesResto][2]);
            $this->contenedor[] = $luchador;
        }
    }

    /**
     * Genera nombres aleatoreos para los participantes.
     * 
     * @param int $numero Numero de nombres a generar.
     * 
     * @return void $contenedor
     */
    private function generarNombresAleatoreos(int $numero): void
    {
        for ($i = 0; $i < $numero; $i++) {
            $luchador = new Luchador();
            $luchador->generarPersonajeAleatoreo();
            $this->contenedor[] = $luchador;
        }
    }

    /**
     * Verificar número de participantes.
     * Verificar que sea de la forma (2^n)
     * 
     * @param int $numero Numero a verificar.
     * 
     * @return bool
     */
    private function verificarNumeroParticipantes(int $numero): bool
    {
        if ($numero < 2) {
            die('Número de participantes insuficiente');
        }
        if ((log($numero, 2) - floor(log($numero, 2))) == 0) {
            return true;
        }
        return false;
    }
}

// Ejecutar la aplicación.
exit((new Torneo())->ejecutar());
