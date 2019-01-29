<?php
namespace Dgfactor\Script\Manipulator;


use Sensio\Bundle\GeneratorBundle\Manipulator\Manipulator;

class KernelManipulator extends Manipulator
{
    protected $listBundles = array();
    protected $endLine = -1;
    protected $kernelFile;
    protected function nextWithComment()
    {
        while ($token = array_shift($this->tokens)) {
            $this->line += substr_count($this->value($token), "\n");

            if (is_array($token) && $token[0] == T_WHITESPACE) {
                continue;
            }

            return $token;
        }
    }

    public function __construct(\SplFileInfo $kernelFile)
    {
        $this->kernelFile = $kernelFile;
        $this->setCode(token_get_all(file_get_contents($kernelFile->getPathname())));
    }

    public function addBundle($bundle)
    {
        if(in_array($bundle, $this->listBundles) || $this->line < 0){
            return false;
        }
        $src = file($this->kernelFile->getPathname());
        $leadingContent = implode('', array_slice($src, 0, $this->line));
        $buncleContent = sprintf("%snew %s(),\n", str_repeat(' ', 12), $bundle);
        $tailContent = implode('', array_slice($src, $this->line));
        file_put_contents($this->kernelFile->getPathname(), $leadingContent.$buncleContent.$tailContent);
        return true;
    }

    /**
     * @return $this
     */
    public function parse()
    {
        while ($token = $this->next()) {

            // $bundles
            if (T_VARIABLE !== $token[0] || '$bundles' !== $token[1]) {
                continue;
            }

            // =
            $this->next();

            // array start with traditional or short syntax
            $token = $this->next();
            if (T_ARRAY !== $token[0] && '[' !== $this->value($token)) {
                break;
            }

            // add the bundle at the end of the array
            while ($token = $this->nextWithComment()) {
                $token = $this->parseBundle($token);

                // look for ); or ];
                if (')' !== $this->value($token) && ']' !== $this->value($token)) {
                    continue;
                }

                if (';' !== $this->value($this->peek())) {
                    continue;
                }

                $this->next();
                $this->endLine = $this->line;
                break 2;
            }
        }
        return $this;
    }

    protected function parseBundle($token)
    {
        $bundleName = '';

        if(is_array($token) && $token[0] == T_NEW){
            while($token = $this->next()){
                if(!is_array($token)) {
                    $this->listBundles[] = $bundleName;
                    return $token;
                }
                $bundleName.=$token[1];
            }
        }

        if(is_array($token) && $token[0] == T_COMMENT){
            if(preg_match('/^\/\/\s*new\s+([\w\\\\]+)/i', $token[1], $match)){
                $this->listBundles[] = $match[1];
                return $token;
            }
        }

        return $token;
    }

}