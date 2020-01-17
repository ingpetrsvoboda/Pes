<?php

namespace Pes\Container;

use Pes\Container\Exception;

use Psr\Container\ContainerInterface;

/**
 * Description of Container
 *
 * @author pes2704
 */
class Container implements ContainerSettingsAwareInterface {

    const INTERFACE_NAME_POSTFIX = 'Interface';
    const INTERFACE_NAME_POSTFIX_LENGTH = -9;  //subst_compare a substr zprava

    /**
     * Kontejner, na který bude použit pokud tento kontejner neobsahuje požadovanou hodnotu.
     *
     * @var ContainerInterface
     */
    protected $delegateContainer;

    protected $useAutogeneratedInterfaceAliases;

    public $containerName;

    /**
     * Obsahuje již vytvořené instance objektů vytvořených voláním get($service).
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Aliasy ke jménu.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Pole generátorů - closure generovaných metodami set() a factory()
     * @var type
     */
    protected $generators = [];

    /**
     * Signalizuje uzamčený kontejner.
     * @var boolean
     */
    private $locked;

    /**
     * Konstruktor.
     *
     * Jako parametr přijímá delegate kontejner, tedy kontejner, na který bude delegován požadavek na službu, pokud tento kontejner
     * službu se zadaným jménem neobsahuje. Dále přijímá bool parametr s dedaultní hodnotou TRUE, který určuje zda bude automaticky
     * použit alias pro jméno služby, které je názvem interface a exituje k ní obdobně pojmenovaná tčída.
     *
     * @param ContainerInterface $delegateContainer Kontejner, který bude vnořen jako delegát.
     * @param type $useAutogeneratedInterfaceAliases
     */
    public function __construct(ContainerInterface $delegateContainer = null, $useAutogeneratedInterfaceAliases=TRUE) {
        $this->delegateContainer = $delegateContainer;
        $this->useAutogeneratedInterfaceAliases = $useAutogeneratedInterfaceAliases;
    }

    /**
     * Nastaví kontejneru vlastnost jméno. Tato metoda slouží pouze pro ladění - umožňuje udžet si přehled, ve kterém konteneru se hledá služba
     * i v případě použití více zanořených delegete kontejnerů
     * @param string $containerName
     */
    public function setContainerName($containerName) {
        $this->containerName = $containerName;
    }

    /**
     * Nastaví definici služby s daným jménem. Služba je volaná metodou get() kontejneru a vrací hodnotu.
     * Služba definovaná metodou set() generuje hodnotu pouze jednou, při prvním volání metody kontejneru get(), další volání metody get() vrací
     * identickou hodnotu. Pokud služba generuje objekt, každé volání get() vrací stejnou instanci objektu.
     * Služba musí být Closure nebo přímo zadaná hodnota. Generování hodnoty zadanou službou probíhá až v okamžiku volání metody get().
     * Pokud je služba typu \Closure, provede se se až v okamžiku volání metody get() kontejneru, jed tedy o lazy load generování hodnoty.
     *
     * Předefinování služby: Při opakovaném volání metody se stejným jménem služby dojde k jejímu předefinování. Pokud již byla vytvořena instance objektu vraceného službou, je tato instance odstraněna (unset)
     * a při dalším volání služby vzniknen nová instance. Pokud je služby, která má být předefinována obsažena v delegate kontejneru, je předefinována služby delegate kontejneru,
     * není vytvořena nová služba v nadřazeném kontejneru.
     *
     * @param string $serviceName
     * @param mixed $service Closure nebo hodnota
     * @return ContainerSettingsAwareInterface
     */
    public function set($serviceName, $service) : ContainerSettingsAwareInterface {
//        if (isset($this->delegateContainer) AND $this->delegateContainer->has($serviceName)) {
//            if ($this->delegateContainer instanceof ContainerSettingsAwareInterface) {
//                return $this->delegateContainer->set($serviceName, $service);
//            } else {
//                throw new Exception\UnableToSetServiceException("Nelze nastavit službu kontehneru. Služba $serviceName je obsažena v delegate kontejneru a ten není typu ContainerSettingsAwareInterface.");
//            }
//        }
        // smaž instanci při předefinování služby
        if (isset($this->instances[$serviceName])) {
            unset($this->instances[$serviceName]);
        }
        if ($service instanceof \Closure) {
            $this->generators[$serviceName] = function() use ($serviceName, $service) {
                        // ještě není instance?
                        if (!isset($this->instances[$serviceName])) {
                            // vytvoř instanci
                            $this->instances[$serviceName] = $service($this);
                        }
                        return $this->instances[$serviceName];
                    };
        } else {
            $this->generators[$serviceName] = function() use ($service) {
                        return $service;  // service je hodnota - nevytvářím instanci - mám hodnotu zde v definici anonymní funkce
                    };
        }
        return $this;
    }

    public function reset($serviceName)  : ContainerSettingsAwareInterface{
        if (isset($this->instances[$serviceName] )) {
            unset($this->instances[$serviceName] );
        }
        return $this;
    }

    /**
     * Nastaví definici služby s daným jménem jako typ factory. Služba je volaná metodou get() kontejneru a vrací hodnotu.
     * Služba definovaná metodou factory() generuje hodnotu vždy znovu, při každém volání metody kontejneru get().
     * Pokud služba generuje objekt, každé volání get() vrací novou instanci objektu.
     * Služba musí být Closure nebo přímo zadaná hodnota. Generování hodnoty zadanou službou probíhá až v okamžiku volání metody get().
     * Pokud je služba typu \Closure, provede se se až v okamžiku volání metody get() kontejneru, jed tedy o lazy load generování hodnoty.
     *
     * @param string $factoryName
     * @param mixed $service Closure nebo hodnota
     * @return ContainerSettingsAwareInterface
     */
    public function factory($factoryName, $service) : ContainerSettingsAwareInterface {
//        if (isset($this->delegateContainer) AND $this->delegateContainer->has($factoryName)) {
//            if ($this->delegateContainer instanceof ContainerSettingsAwareInterface) {
//                return $this->delegateContainer->factory($factoryName, $service);
//            } else {
//                throw new Exception\UnableToSetServiceException("Nelze nastavit factory kontehneru. Factory $factoryName je obsažena v delegate kontejneru a ten není typu ContainerSettingsAwareInterface.");
//            }
//        }
        if ($service instanceof \Closure) {
            $this->generators[$factoryName] = function() use ($factoryName, $service) {
                        return $service($this);
                    };
        } else {
            $this->generators[$factoryName] = function() use ($service) {
                        return $service;  // service je hodnota
                    };
        }
        return $this;
    }

    /**
     * Nastaví alias ke skutečnému jménu služby. Volání služby jménem alias vede na volání služby se skutečným jménem.
     * Třída nepodporuje víceúrovňové alias (alias k aliasu, který je aliasem ke jménu atd.)
     * Alias je aliasem ke službě kontejneru, kde je definován nebo ke službě vnořeného delegate kontejneru.
     *
     * @param string $alias
     * @param string $name
     * @return ContainerSettingsAwareInterface
     */
    public function alias($alias, $name) : ContainerSettingsAwareInterface {
//        if (isset($this->delegateContainer) AND $this->delegateContainer->has($alias)) {
//            if ($this->delegateContainer instanceof ContainerSettingsAwareInterface) {
//                return $this->delegateContainer->alias($alias, $name);
//            } else {
//                throw new Exception\UnableToSetServiceException("Nelze nastavit factory kontehneru. Factory $alias je obsažena v delegate kontejneru a ten není typu ContainerSettingsAwareInterface.");
//            }
//        }
        $this->aliases[$alias] = $name;
        return $this;
    }

###############################################

    /**
     * Existuje definice služby?
     *
     * @param string $serviceName Jméno hledané služby
     * @return bool
     */
    public function has($serviceName) {
        if (isset($this->generators[$serviceName])) {     // pole $this->has obsahuje jen položky definované v této instanci kontejneru
            return TRUE;
        }
        $realName = $this->realName($serviceName);
        if (isset($this->generators[$realName])) {
            return TRUE;
        }
        if (isset($this->delegateContainer) AND $this->delegateContainer->has($serviceName)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Vrací výsledek volání služby zadaného jména.
     *
     * @param string $serviceName Jméno volané služby.
     * @return mixed Návratová hodnota vracená službou.
     * @throws NotFoundException Služba nenalezena
     */
    public function get($serviceName) {
        if (isset($this->generators[$serviceName])) {
            return $this->generators[$serviceName]();
        }
        $realName = $this->realName($serviceName);
        if (isset($this->generators[$realName])) {
            return $this->generators[$realName]();
        }
        if (isset($this->delegateContainer) AND $this->delegateContainer->has($serviceName)) {
            return $this->delegateContainer->get($serviceName);
        }
        if (isset($this->delegateContainer) AND $this->delegateContainer->has($serviceName)) {
            return $this->delegateContainer->get($serviceName);
        }
        throw new Exception\NotFoundException("Volání služby kontejneru get('$serviceName') selhalo. Požadovaná služba kontejneru se jménem: '$serviceName' neexistuje, nebyla nastavena.");
    }

    /**
     * Pokud je v instanci kontejneru zadano jméno jako alias, metoda vrací zadané skutečné jméno služby.
     * Pokud alias zadán není, ale je nastavena hodnota instanční proměnné $useAutogeneratedInterfaceAliases na TRUE (default),
     * jméno hledané služby odpovídá existujícímu interface a končí řetězcem zadaným konstantou INTERFACE_NAME_POSTFIX a existuje třída se jménem
     * odpovídajícím jménu interface po odtržení přípony dané konstatntou INTERFACE_NAME_POSTFIX, pak metoda vrací jméno takové třídy.
     *
     * Příklad:
     * self:INTERFACE_NAME_POSTFIX = 'Interface'
     * volání metody ->realName('KlokociInterface') vrací sktečné jméno 'Klokoci' (pokud existuje KlokociInterface i Klokoci)
     *
     *
     * @param string $serviceName
     * @return string
     */
    protected function realName($serviceName) {
        if (isset($this->aliases[$serviceName])) {
            $realName = $this->aliases[$serviceName];
        } elseif ($this->useAutogeneratedInterfaceAliases) {
            if (substr_compare( $serviceName, self::INTERFACE_NAME_POSTFIX, self::INTERFACE_NAME_POSTFIX_LENGTH) === 0) {
                if (interface_exists($serviceName)) {
                    $realName = substr($serviceName, 0, self::INTERFACE_NAME_POSTFIX_LENGTH);
                    if (class_exists($realName)) {
                        $this->aliases[$serviceName] = $realName;
                    } else {
                        throw new Exception\NotFoundException("Nelze provést autowire k interface $serviceName. Definice třídy $realName nebyla nalezena.");
                    }
                } else {
                    throw new Exception\NotFoundException("Nelze provést autowire k požadovanému jménu interface $serviceName. Definice interface $serviceName nebyla nalezena.");
                }
            } else {
                $realName = $serviceName;
            }
        } else {
            $realName = $serviceName;
        }
        return $realName;
    }

    /**
     * Pokud:
     * - je zapnuto automatické generování aliasů k interface - paramater kontruktoru $useAutogeneratedInterfaceAliases byl TRUE (dafault je TRUE)
     * - a existuje interface se zadaným jménem služby
     * - a existuje automatický překlad jména služby (interface) na jméno třídy
     *
     * pak nastaví automaticky přeložené jméno třídy jako alias k zadanému jménu služby (interface).
     *
     * Pro automatický překlad jména interface se používá protected metoda translateInterfaceName(). Tuto metodu je možno přetížit a změnit tak
     * defaultní překlad.
     *
     * @param type $serviceName
     */
    private function createAutoInterfaceAlias($serviceName) {
        if ($this->useAutogeneratedInterfaceAliases AND interface_exists($serviceName)) {
            $realName = $this->translateInterfaceName($serviceName);
            if (isset($realName) AND $realName) {     // moje při neuspěchu vrací NULL, ale potomci vrací kdovíco
                $this->aliases[$serviceName] = $realName;
            }
        }
        return $realName;
    }

    /**
     * Metoda provádí automatický překlad jména interface na jméno třídy. Tuto metodu je možno přetížit a změnit tak
     * defaultní překlad.
     *
     * Tato metoda zjistí zda jméno interface končí řetezcem definovaným konstatou třídy INTERFACE_NAME_POSTFIX, pokud ano,
     * vytvoří jméno třídy odstraněním tohoto řetezce (přípony) ze jména interface.
     *
     * Alternativně je možno změnit automatický překlad přetížením třídy kontejneru třídou, které pouze předefinuje konstanty
     * INTERFACE_NAME_POSTFIX a INTERFACE_NAME_POSTFIX_LENGTH, pak je metoda překladu zachována a mění se jen očekávaná přípona v názvu interface.
     * Konstanta INTERFACE_NAME_POSTFIX_LENGTH udává délku přípony.
     *
     * Metoda musí vracet string v případě úspěchu nebo NULL v případě neúspěchu.
     *
     * @param type $serviceName
     * @return string || NULL
     */
    protected function translateInterfaceName($serviceName) {
        $len = -1*self::INTERFACE_NAME_POSTFIX_LENGTH;
        if (substr_compare( $serviceName, self::INTERFACE_NAME_POSTFIX, $len) === 0){
            return substr($serviceName, 0, $len);
        }
    }
}