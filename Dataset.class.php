<?php
class Dataset
{
    private $nat;
    private $nats;
    private $output;
    private $seed;
    private $lists;
    private $results;
    private $randomSeed = false;
    private $constantTime = 1437996378;
    private $version = "0.8";

    private $format = "json";

    public function __construct($seed) {
        $this->fetchNats();  // Scan nats folder for available nationalities

        // We append the country hex code at the end to determine
        // which nationality a seed is associated with.
        if (strlen($seed) == 18) {
            $this->setNat($this->getNats()[hexdec(substr($seed, -2))]);
            $this->setSeed($seed);
        } else if ($seed == null) {
            $this->randomSeed = true;
            $this->defaultSeed();
        } else {
            $this->setSeed($seed);
        }

        $this->seedRNG();
    }

    // Choose a random nationality
    public function chooseRandomNat() {
        $this->setNat($this->getNats()[array_rand($this->getNats())]);
    }

    // Verify if provided nationality is valid
    public function validNat($nat) {
        return in_array(strtoupper($nat), $this->getNats());
    }

    // Generate results
    public function generate($results = 10) {
        require_once("nats/" . $this->getNat() . "/inject.php");   // Loads in unique nat injector
        $this->setResults($results);

        if ($this->getSeed() == null) {
            $this->defaultSeed();
        }

        $this->genLists();  // Generate all of the results for the lists
        $output  = [];      // Holds all of the results
        $current = [];      // Current result being generated

        // Generate basic outline for nationality
        for ($i = 0; $i < $results; $i++) {
            $current["gender"] = $this->lists["gender"][$i];

            $name = $this->randomName($this->lists["gender"][$i], $i);
            $current["name"]["title"] = $this->lists["gender"][$i] == "male" ? "mr" : array("miss", "mrs", "ms")[mt_rand(0, 2)];
            $current["name"]["first"] = $name[0];
            $current["name"]["last"]  = $name[1];

            $current["location"]["street"] = mt_rand(1000, 9999) . " " . $this->lists["street"][$i];
            $current["location"]["city"]   = $this->lists["cities"][$i];
            $current["location"]["state"]  = $this->lists["states"][$i];
            $current["location"]["zip"]    = mt_rand(10000, 99999);

            $current["email"   ] = $name[0] . "." . $name[1] . "@example.com";
            $current["username"] = $this->lists["user1"][$i] . $this->lists["user2"][$i] . mt_rand(100, 999);
            $current["password"] = $this->lists["passwords"][$i];

            $current["salt"]   = $this->random(2, 8);
            $current["md5"]    = md5($this->lists["passwords"][$i] . $current["salt"]);
            $current["sha1"]   = sha1($this->lists["passwords"][$i] . $current["salt"]);
            $current["sha256"] = hash('sha256', ($this->lists["passwords"][$i] . "blah"));

            $current["registered"] = mt_rand(915148800, $this->constantTime);
            $current["dob"] = mt_rand(0, $this->constantTime);

            if ($this->getNat() != "LEGO") {
                $id = $this->lists["gender"][$i] == "male" ? mt_rand(0, 99) : mt_rand(0, 96);
                $genderText = $this->lists["gender"][$i] == "male" ? "men" : "women";
            } else {
                $id = mt_rand(0, 9);
                $genderText = "lego";
            }

            $base = "https://randomuser.me/api/";
            $current["picture"]["large"]     = $base . "portraits/" . $genderText . "/" . $id . ".jpg";
            $current["picture"]["medium"]    = $base . "portraits/med/" . $genderText . "/" . $id . ".jpg";
            $current["picture"]["thumbnail"] = $base . "portraits/thumb/" . $genderText . "/" . $id . ".jpg";

            $inject::execute($current, "Dataset::random");  // Inject unique fields for nationality

            $output[] = array("user" => $current);   // Append new result to output array
        }

        // Add seed to 1st result
        $data = array(
            "results" => $output
        );

        $data["nationality"] = $this->getNat();
        $data["seed"]        = $this->getSeed() . ($this->isRandom() && $this->getNat() != "LEGO" ? sprintf("%02x", array_search($this->getNat(), $this->getNats())) : null);
        $data["version"]     = $this->getVersion();
        $this->setOutput($data);
    }

    private function randomName($gender, $index) {
        return array($this->lists[$gender . "_first"][$index], $this->lists["last"][$index]);
    }

    // Picks random lines from all of the lists
    private function genLists() {
        for ($i = 0; $i < $this->results; $i++) {
            if ($this->getNat() == "LEGO") {
                $this->lists["gender"][] = "male";
            } else if ($this->getGender() == null) {
                $this->lists["gender"][] = mt_rand(0, 1) ? "male" : "female";
            } else {
                $this->lists["gender"][] = $this->getGender();
            }
        }

        // Mass generate list results for lists unique to nationalities
        $lists = array("cities", "female_first", "last", "male_first", "states", "street");
        foreach ($lists as $list) {

            $path = "nats/" . $this->getNat() . "/lists/" . $list . ".txt";
            if (file_exists($path)) {
                $file = file("nats/" . $this->getNat() . "/lists/" . $list . ".txt");
                $len = count($file);

                for ($a = 0; $a < $this->results; $a++) {
                    $result = trim($file[mt_rand(0, $len - 1)]);
                    $this->lists[$list][] = mb_check_encoding($result, 'UTF-8') ? $result : utf8_encode($result);
                }
            }
        }

        // Mass generate list results for lists common between nationalities
        $common = array("passwords", "title", "user1", "user2");
        foreach ($common as $list) {

            $file = file("nats/common/" . $list . ".txt");
            $len = count($file);

            for ($a = 0; $a < $this->results; $a++) {
                $result = trim($file[mt_rand(0, $len - 1)]);
                $this->lists[$list][] = mb_check_encoding($result, 'UTF-8') ? $result : utf8_encode($result);
            }
        }
    }

    private function defaultSeed() {
        // Create a default seed if none provided
        // Do this before generating random hex seed
        $seed = $this->makeSeed();
        srand($seed);
        mt_srand($seed);

        $this->setSeed($this->random(1, 16));
    }

    // Generate a default seed since PHP's automatic seeding is unreliable at times.
    private function makeSeed() {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $sec + ((float) $usec * 100000);
    }

    // Fetch all the nationalites from nats folder
    private function fetchNats() {
        $this->nats = array_values(array_diff(scandir("nats"), array("..", ".", "common", "LEGO")));
    }

    // Generates random hex string $length long
    public static function random($mode, $length) {
        $result = "";
        
        if ($mode == 1) {
            $chars = "abcdef1234567890";
        } else if ($mode == 2) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        } else if ($mode == 3) {
            $chars = "0123456789";
        } else if ($mode == 4) {
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $result;
    }

    public function seedRNG() {
        $seed = $this->getSeed();

        if (strlen($seed) == 18) {
            $seed = substr($seed, 0, 16);
        }

        $seed = hexdec(substr(md5($seed), 0, 8));
        srand($seed);
        mt_srand($seed);
    }

    // Determine the format to convert data to
    // Outputs the data in the requested format type
    public function output() {
        $fmt = $this->getFormat();

        if ($fmt == "sql") {
            return json_encode($this->getOutput(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else if ($fmt == "yaml") {
            return yaml_emit($this->getOutput(), YAML_UTF8_ENCODING);
        } else if ($fmt == "csv") {
            return json_encode($this->getOutput(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            return json_encode($this->getOutput(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }

    /*public function getOutput() {
    // Display sql
    if ($this->getFormat() == "sql") {
    $file = JSONToSQL(json_encode(array("results" => $return, "info" => $msg), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    if ($dl == "true") { header('Content-Length: ' . filesize($file)); }
    echo file_get_contents($file);
    unlink($file);
    die;
    } else if ($fmt == "csv") {
    $file = JSONToCSV(json_encode(array("results" => $return, "info" => $msg), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    if ($dl == "true") { header('Content-Length: ' . filesize($file)); }
    echo file_get_contents($file);
    unlink($file);
    die;
    } /*else if ($fmt == "xml") {
    $file = arrayToXML(array("results" => $return, "info" => $msg), new SimpleXMLElement('<results/>'));
    #$file = arrayToXml(array("results" => $return, "info" => $msg));
    if ($dl == "true") { header('Content-Length: ' . filesize($file)); }
    echo file_get_contents($file);
    unlink($file);
    die;
    } else if ($fmt == "yaml") {
    $file = yaml(array("results" => $return));
    if ($dl == "true") { header('Content-Length: ' . filesize($file)); }
    echo file_get_contents($file);
    unlink($file);
    die;
    } else {
    if ($noinfo) {
    $file = JSON((json_encode(array("results" => $return), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)));
    } else if ($onlyinfo) {
    $file = JSON((json_encode(array("info" => $msg), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)));
    } else {
    $file = JSON((json_encode(array("results" => $return, "info" => $msg), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)));
    }
    if ($dl == "true") { header('Content-Length: ' . filesize($file)); }
    echo file_get_contents($file);
    unlink($file);
    die;
    }
    }
    */

    // Getters
    public function isRandom() { return $this->randomSeed; }
    public function getNats() { return $this->nats; }
    public function getNat() { return $this->nat; }
    private function getSeed() { return $this->seed; }
    private function getGender() { return $this->gender; }
    private function getOutput() { return $this->output; }
    private function getFormat() { return $this->format; }
    private function getVersion() { return $this->version; }

    // Setters
    public function setNat($nat) { $this->nat = strtoupper($nat); }
    public function setSeed($seed) { $this->seed = $seed; }
    public function setGender($gender) { $this->gender = $gender; }
    public function setResults($results) { $this->results = $results; }
    public function setOutput($output) { $this->output = $output; }
    public function setFormat($format) { $this->format = $format; }
}
?>
