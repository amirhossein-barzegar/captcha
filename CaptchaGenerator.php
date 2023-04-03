<?php

class CaptchaGenerator
{
    // Each character width in captcha image
    private const CHARACTER_WIDTH = 40;
    
    // Available formates for captcha image
    private const AVAILABLE_FORMATES = [
        'jpg', 'jpeg', 'png'
    ];
    
    // Available fonts for captcha image
    private const AVAILABLE_FONTS = [
        'verdana', 'libre', 'lilita'
    ];
    
    // Height for captcha image
    private int $height = 50;
    
    // Available letters for captcha
    private const LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    
    /**
     * @param string $fontName
     * @param string $format
     * @param bool $isDark
     */
    public function __construct(protected string $fontName = 'verdana',protected string $format = 'jpg',protected bool $isDark = false)
    {}
    
    /**
     * Generate random captcha image
     * @param int $length
     *
     * @return bool
     */
    public function generate(int $length): bool
    {
        $width = (self::CHARACTER_WIDTH * $length) + pow($length,2);
        $font = '';
        # Import correct font path or error
        if (in_array($this->fontName, self::AVAILABLE_FONTS)) {
            $font = realpath("./fonts/{$this->fontName}.ttf");
        } else {
            try {
                throw new Exception("Font name must be verdana, libre, lilita! but <b>{$this->fontName}</b> passed.");
            } catch(Exception $e) {
                $_SESSION['error'] = $e->getMessage();
//                die($e->getMessage());
            }
        }
        
        # Generate random letters for captcha
        $captchaCode = substr(str_shuffle(self::LETTERS),0,$length);
        
        # Make captcha image
        $captchaImg = imagecreatetruecolor($width,$this->height);
        
        # Is dark mode
        if ($this->isDark) {
            # Make dark background in dark mode
            $background = imagecolorallocate($captchaImg,rand(0,50),rand(0,50),rand(0,50));
            # Make light noise in dark mode
            $noiseColor = imagecolorallocate($captchaImg,rand(230,255),rand(230,255),rand(230,255));
        } else {
            # Make light background in light mode
            $background = imagecolorallocate($captchaImg,rand(230,255),rand(230,255),rand(230,255));
            # Make dark noise in light mode
            $noiseColor = imagecolorallocate($captchaImg,rand(0,50),rand(0,50),rand(0,50));
        }
        
        # Add background to captcha image
        imagefill($captchaImg, 0,0, $background);
        
        # Add noise points on captcha image
        for($y = 0; $y < $this->height; $y++)
        {
            for($x = 0; $x < $width; $x++) {
                if (mt_rand(0,20) == 7) imagesetpixel($captchaImg, $x,$y, $noiseColor);
            }
        }
        
        $step = round($width /$length);
        
        for($iterate = 1; $iterate <= $length; $iterate++) {
            # Is dark mode
            if ($this->isDark) {
                # Make light captcha color in dark mode
                $textColor = imagecolorallocate($captchaImg,rand(200,255),rand(200,255),rand(200,255));
            } else {
                # Make dark captcha color in light mode
                $textColor = imagecolorallocate($captchaImg,rand(0,50),rand(0,50),rand(0,50));
            }
    
            # Start point for each letters
            $incrementStep = ($step * $iterate) - $step;
            # X position for each letter
            $x = $incrementStep;
            # Y position for each letter
            $y = $this->height * 0.7;
            $textSize = $this->height * 0.6;
            $textAngle = rand(-10, 10);
            $currentLetter = $captchaCode[$iterate-1];
            # Adding captcha code on captcha image
            imagettftext($captchaImg, $textSize, $textAngle, $x, $y, $textColor, $font, $currentLetter);
        }
        
        # Show captcha image or error
        if (in_array($this->format ,self::AVAILABLE_FORMATES)) {
            # Indicate image type will be display
            header("Content-type: image/$this->format");
            return imagejpeg($captchaImg);
        } else {
            try {
                throw new Exception("Format must be jpg, jpeg or png! but <b>{$this->format}</b> given.");
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
//                die($e->getMessage());
            }
        }
    }
}
