<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'        => 'Polje :attribute mora biti sprejeto.',
    'active_url'      => 'Polje :attribute ni veljaven URL naslov.',
    'after'           => 'Polje :attribute mora biti za datumom :date.',
    'after_or_equal'  => 'Polje :attribute mora biti za ali enako :date.',
    'alpha'           => 'Polje :attribute lahko vsebuje samo črke.',
    'alpha_dash'      => 'Polje :attribute lahko vsebuje samo črke, številke in pomišljaje.',
    'alpha_num'       => ':attribute lahko vsebuje samo črke in številke.',
    'array'           => 'Polje :attribute mora biti polje / tabela.',
    'before'          => 'Polje :attribute mora biti pred datumom :date.',
    'before_or_equal' => 'Polje :attribute mora biti pred ali enako :date.',
    'between'         => [
        'numeric' => 'Polje :attribute mora biti med :min in :max.',
        'file'    => 'Polje :attribute mora biti med :min in :max kilobajti.',
        'string'  => 'Polje :attribute mora vsebovati med :min in :max znakov.',
        'array'   => 'Polje :attribute mora vsebovati med :min in :max elementov.',
    ],
    'boolean'        => "Polje :attribute mora biti 'true' ali 'false'",
    'confirmed'      => 'Potrditveno polje :attribute se ne ujema.',
    'date'           => 'Polje :attribute ni veljaven datum.',
    'date_format'    => 'Polje :attribute se ne ujema s formatom :format.',
    'different'      => 'Polji :attribute in :other se morata razlikovati.',
    'digits'         => 'Polje :attribute mora imeti :digits števk.',
    'digits_between' => 'Polje :attribute mora imeti med :min in :max števk.',
    'dimensions'     => 'Polje :attribute ima napačne dimenzije slike.',
    'distinct'       => 'Polje :attribute je duplikat.',
    'email'          => 'Polje :attribute mora biti veljaven email naslov.',
    'exists'         => 'Izbrana vrednost polja :attribute je neveljavna.',
    'file'           => 'Polje :attribute mora biti datoteka.',
    'filled'         => 'Polje :attribute mora vsebovati vrednost.',
    'image'          => 'Polje :attribute mora biti slika.',
    'in'             => 'Izbrana vrednost polja :attribute je neveljavna.',
    'in_array'       => 'Polje :attribute ne obstaja v :other.',
    'integer'        => 'Polje :attribute mora biti število.',
    'ip'             => 'Polje :attribute mora biti veljaven IP naslov.',
    'ipv4'           => 'Polje :attribute mora biti veljaven IPv4 naslov.',
    'ipv6'           => 'Polje :attribute mora biti veljaven IPv6 naslov.',
    'json'           => 'Polje :attribute mora biti veljaven JSON tekst.',
    'max'            => [
        'numeric' => 'Polje :attribute ne sme biti večje od :max.',
        'file'    => 'Polje :attribute ne sme imeti več kot :max kilobajtov.',
        'string'  => 'Polje :attribute ne sme vsebovati več kot :max znakov.',
        'array'   => 'Polje :attribute ne sme vsebovati več kot :max elementov.',
    ],
    'mimes'     => 'Polje :attribute mora biti datoteka tipa: :values.',
    'mimetypes' => 'Polje :attribute mora biti datoteka tipa: :values.',
    'min'       => [
        'numeric' => 'Polje :attribute mora biti vsaj :min.',
        'file'    => 'Polje :attribute mora imeti vsaj :min kilobajtov.',
        'string'  => 'Polje :attribute mora imeti vsaj :min znakov.',
        'array'   => 'Polje :attribute mora vsebovati vsaj :min elementov.',
    ],
    'not_in'               => 'Izbrana vrednost polja :attribute je neveljavna.',
    'numeric'              => 'Polje :attribute mora biti število.',
    'present'              => 'Polje :attribute mora biti prisotno.',
    'regex'                => 'Format polja :attribute je neveljaven.',
    'required'             => 'Polje :attribute je obvezno.',
    'required_if'          => 'Polje :attribute je obvezno, če je :other enak :value.',
    'required_unless'      => 'Polje :attribute je obvezno, razen če je :other v :values.',
    'required_with'        => 'Polje :attribute je obvezno, če je :values prisoten.',
    'required_with_all'    => 'Polje :attribute je obvezno, če so :values prisotni.',
    'required_without'     => 'Polje :attribute je obvezno, če :values ni prisoten.',
    'required_without_all' => 'Polje :attribute je obvezno, če :values niso prisotni.',
    'same'                 => 'Polje :attribute in :other se morata ujemati.',
    'size'                 => [
        'numeric' => 'Polje :attribute mora biti velikosti :size.',
        'file'    => 'Polje :attribute mora biti velikosti :size kilobajtov.',
        'string'  => 'Polje :attribute mora vsebovati :size znakov.',
        'array'   => 'Polje :attribute mora vsebovati :size elementov.',
    ],
    'string'   => 'Polje :attribute mora biti tekst.',
    'timezone' => 'Polje :attribute mora biti časovna cona.',
    'unique'   => 'Polje :attribute je že v uporabi.',
    'uploaded' => 'Nalaganje vsebine polja :attribute ni uspelo.',
    'url'      => 'Format polja :attribute je neveljaven.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'password' => [
            'confirmed' => 'Geslo se mora ujemati s potrditvenim geslom.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name'     => "ime",
        'email'    => "e-mail",
        'password' => "geslo",
        'school'   => 'šola',
        'grade'    => 'razred',
    ],
];
