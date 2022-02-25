<?php

/**
 * Réponse Question 1 : fonction qui permet de retourner des plages de valeurs communes à toutes les données en entrée.
 * 
 * Pour chaque tableau en entrée, on a le  minimum et le maximum.
 * On va ensuite comparer aux tableaux retournés et si les valeurs du tableau en entrée se chevauchent avec celles comparées, on met à jour les données retournées en prenant le minimum et le maximum
 */

namespace App\Test;

use PHPUnit\Framework\TestCase;

class FooTest extends TestCase
{

    private function foo(array $input)
    {
        // tri pour avoir les tableaux d'entrées par ordre croissant
        sort($input);
        // initialisation du tableau de résultat
        $output = [];

        // on parcourt ensuite le tableau d'entrée
        foreach ($input as $data) {
            // initialisation d'une variable permettant de savoir si on a fait une maj ou pas du tableau de résultat
            $matched = false;
            // on parcourt le tableau de résultat pour faire la comparaison
            if (count($output)) {
                foreach ($output as &$outputData) {
                    // si les 2 tableaux se chevauchent, on met à jour les valeurs
                    if (count(array_intersect(range($outputData[0], $outputData[1]), range($data[0], $data[1])))) {
                        $matched = true;
                        $outputData[0] = min([$data[0], $outputData[0]]);
                        $outputData[1] = max([$data[1], $outputData[1]]);
                    }
                }
            }

            // si aucune donnée n'a été mise à jour, c'est que ce tableau d'entrée ne se chevauche pas avec d'autre, on l'insère directement dans le tableau de résultats
            if (!$matched) {
                $output[] = $data;
            }
        }

        return $output;
    }

    public function testFooFunction()
    {
        $arrayTests = [
            [
                'input' => [[0, 3], [6, 10]],
                'expectedResult' => [[0, 3], [6, 10]]
            ],
            [
                'input' => [[0, 5], [3, 10]],
                'expectedResult' => [[0, 10]]
            ],
            [
                'input' => [[0, 5], [2, 4]],
                'expectedResult' => [[0, 5]]
            ],
            [
                'input' => [[7, 8], [3, 6], [2, 4]],
                'expectedResult' => [[2, 6], [7, 8]]
            ],
            [
                'input' => [[3, 6], [3, 4], [15, 20], [16, 17], [1, 4], [6, 10], [3, 6]],
                'expectedResult' => [[1, 10], [15, 20]]
            ],
        ];

        foreach ($arrayTests as $test) {
            $this->assertSame($test['expectedResult'], $this->foo($test['input']));
        }
    }
}
