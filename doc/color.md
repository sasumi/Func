# Color
 > Color Enhancement Functions

## 1. color_hex2rgb($hex_color): array
Convert hexadecimal color format to RGB format (array)
#### Parameters
 - {string} *$hex_color* #ff00bb

#### Returns
 - array 

## 2. color_rgb2hex($rgb,$prefix): string
Convert RGB format to hexadecimal color format
#### Parameters
 - {array} *$rgb* [r,g,b]
 - {string} *$prefix* 

#### Returns
 - string 

## 3. color_rgb2hsl($rgb): float[]
Convert RGB format to HSL format
#### Parameters
 - {array} *$rgb* 

#### Returns
 - float[] [h,s,l]

## 4. color_hsl2rgb($hsl): int[]
Convert HSL format to RGB format
#### Parameters
 - {array} *$hsl* [h,s,l]

#### Returns
 - int[] [r,g,b]

## 5. color_rgb2cmyk($rgb): array
Convert RGB format to CMYK format
#### Parameters
 - {array} *$rgb* 

#### Returns
 - array [c,m,y,k]

## 6. cmyk_to_rgb($cmyk): int[]
Convert CMYK format to RGB format
#### Parameters
 - {array} *$cmyk* 

#### Returns
 - int[] [r,g,b]

## 7. color_rgb2hsb($rgb,$accuracy): array
Convert RGB format to HSB format
#### Parameters
 - {array} *$rgb* [r,g,b]
 - {int} *$accuracy* 

#### Returns
 - array 

## 8. color_hsb2rgb($hsb,$accuracy): int[]
Convert HSB format to RGB format
#### Parameters
 - {array} *$hsb* [h,s,b]
 - {int} *$accuracy* 

#### Returns
 - int[] [r,g,b]

## 9. color_molarity($color_val,$inc_pec): array|string
Calculate the molarity of a color
#### Parameters
 - {string|array} *$color_val* HEX color string, or RGB array
 - {float} *$inc_pec* range of percent, from -99 to 99

#### Returns
 - array|string 

## 10. color_rand(): string
Random color

#### Returns
 - string 

## 11. _hsl_rgb_low()
## 12. _hsl_rgb_high()
## 13. _rgb_hsl_delta_rgb()
## 14. _rgb_hsl_hue()


