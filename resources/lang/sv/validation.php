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

    'accepted' => ':attribute-fältet måste accepteras.',
    'accepted_if' => ':attribute-fältet måste accepteras när :other är :value.',
    'active_url' => ':attribute-fältet måste vara en giltig URL.',
    'after' => ':attribute-fältet måste vara ett datum efter :date.',
    'after_or_equal' => ':attribute-fältet måste vara ett datum efter eller lika med :date.',
    'alpha' => ':attribute-fältet får endast innehålla bokstäver.',
    'alpha_dash' => ':attribute-fältet får endast innehålla bokstäver, siffror, bindestreck och understreck.',
    'alpha_num' => ':attribute-fältet får endast innehålla bokstäver och siffror.',
    'array' => ':attribute-fältet måste vara en array.',
    'ascii' => ':attribute-fältet får endast innehålla ettbyte alfanumeriska tecken och symboler.',
    'before' => ':attribute-fältet måste vara ett datum innan :date.',
    'before_or_equal' => ':attribute-fältet måste vara ett datum innan eller lika med :date.',
    'between' => [
        'array' => ':attribute-fältet måste ha mellan :min och :max objekt.',
        'file' => ':attribute-fältet måste vara mellan :min och :max kilobyte.',
        'numeric' => ':attribute-fältet måste vara mellan :min och :max.',
        'string' => ':attribute-fältet måste vara mellan :min och :max tecken.',
    ],
    'boolean' => ':attribute-fältet måste vara sant eller falskt.',
    'can' => ':attribute-fältet innehåller ett otillåtet värde.',
    'confirmed' => ':attribute-fältets bekräftelse matchar inte.',
    'current_password' => 'Lösenordet är fel.',
    'date' => ':attribute-fältet måste vara ett giltigt datum.',
    'date_equals' => ':attribute-fältet måste vara ett datum lika med :date.',
    'date_format' => ':attribute-fältet måste matcha formatet :format.',
    'decimal' => ':attribute-fältet måste ha :decimal decimaler.',
    'declined' => ':attribute-fältet måste avslås.',
    'declined_if' => ':attribute-fältet måste avslås när :other är :value.',
    'different' => ':attribute-fältet och :other måste vara olika.',
    'digits' => ':attribute-fältet måste vara :digits siffror.',
    'digits_between' => ':attribute-fältet måste vara mellan :min och :max siffror.',
    'dimensions' => ':attribute-fältet har ogiltiga bilddimensioner.',
    'distinct' => ':attribute-fältet har ett duplicerat värde.',
    'doesnt_end_with' => ':attribute-fältet får inte sluta med något av följande: :values.',
    'doesnt_start_with' => ':attribute-fältet får inte börja med något av följande: :values.',
    'email' => ':attribute-fältet måste vara en giltig e-postadress.',
    'ends_with' => ':attribute-fältet måste sluta med ett av följande: :values.',
    'enum' => 'Den valda :attribute är ogiltig.',
    'exists' => 'Den valda :attribute är ogiltig.',
    'extensions' => ':attribute-fältet måste ha en av följande filändelser: :values.',
    'file' => ':attribute-fältet måste vara en fil.',
    'filled' => ':attribute-fältet måste ha ett värde.',
    'gt' => [
        'array' => ':attribute-fältet måste ha fler än :value objekt.',
        'file' => ':attribute-fältet måste vara större än :value kilobyte.',
        'numeric' => ':attribute-fältet måste vara större än :value.',
        'string' => ':attribute-fältet måste vara längre än :value tecken.',
    ],
    'gte' => [
        'array' => ':attribute-fältet måste ha :value objekt eller fler.',
        'file' => ':attribute-fältet måste vara större än eller lika med :value kilobyte.',
        'numeric' => ':attribute-fältet måste vara större än eller lika med :value.',
        'string' => ':attribute-fältet måste vara längre än eller lika med :value tecken.',
    ],
    'hex_color' => ':attribute-fältet måste vara en giltig hexadecimalt färg.',
    'image' => ':attribute-fältet måste vara en bild.',
    'in' => 'Den valda :attribute är ogiltig.',
    'in_array' => ':attribute-fältet måste finnas i :other.',
    'integer' => ':attribute-fältet måste vara ett heltal.',
    'ip' => ':attribute-fältet måste vara en giltig IP-adress.',
    'ipv4' => ':attribute-fältet måste vara en giltig IPv4-adress.',
    'ipv6' => ':attribute-fältet måste vara en giltig IPv6-adress.',
    'json' => ':attribute-fältet måste vara en giltig JSON-sträng.',
    'list' => ':attribute-fältet måste vara en lista.',
    'lowercase' => ':attribute-fältet måste vara små bokstäver.',
    'lt' => [
        'array' => ':attribute-fältet måste ha mindre än :value objekt.',
        'file' => ':attribute-fältet måste vara mindre än :value kilobyte.',
        'numeric' => ':attribute-fältet måste vara mindre än :value.',
        'string' => ':attribute-fältet måste vara kortare än :value tecken.',
    ],
    'lte' => [
        'array' => ':attribute-fältet får inte ha mer än :value objekt.',
        'file' => ':attribute-fältet måste vara mindre än eller lika med :value kilobyte.',
        'numeric' => ':attribute-fältet måste vara mindre än eller lika med :value.',
        'string' => ':attribute-fältet måste vara kortare än eller lika med :value tecken.',
    ],
    'mac_address' => ':attribute-fältet måste vara en giltig MAC-adress.',
    'max' => [
        'array' => ':attribute-fältet får inte ha mer än :max objekt.',
        'file' => ':attribute-fältet får inte vara större än :max kilobyte.',
        'numeric' => ':attribute-fältet får inte vara större än :max.',
        'string' => ':attribute-fältet får inte vara längre än :max tecken.',
    ],
    'max_digits' => ':attribute-fältet får inte ha fler än :max siffror.',
    'mimes' => ':attribute-fältet måste vara en fil av typen: :values.',
    'mimetypes' => ':attribute-fältet måste vara en fil av typen: :values.',
    'min' => [
        'array' => ':attribute-fältet måste ha minst :min objekt.',
        'file' => ':attribute-fältet måste vara minst :min kilobyte.',
        'numeric' => ':attribute-fältet måste vara minst :min.',
        'string' => ':attribute-fältet måste vara minst :min tecken.',
    ],
    'min_digits' => ':attribute-fältet måste ha minst :min siffror.',
    'missing' => ':attribute-fältet måste saknas.',
    'missing_if' => ':attribute-fältet måste saknas när :other är :value.',
    'missing_unless' => ':attribute-fältet måste saknas om inte :other är :value.',
    'missing_with' => ':attribute-fältet måste saknas när :values är närvarande.',
    'missing_with_all' => ':attribute-fältet måste saknas när :values är närvarande.',
    'multiple_of' => ':attribute-fältet måste vara ett multiplum av :value.',
    'not_in' => 'Den valda :attribute är ogiltig.',
    'not_regex' => ':attribute-fältets format är ogiltigt.',
    'numeric' => ':attribute-fältet måste vara ett nummer.',
    'password' => [
        'letters' => ':attribute-fältet måste innehålla minst en bokstav.',
        'mixed' => ':attribute-fältet måste innehålla minst en versal och en gemen bokstav.',
        'numbers' => ':attribute-fältet måste innehålla minst ett nummer.',
        'symbols' => ':attribute-fältet måste innehålla minst en symbol.',
        'uncompromised' => 'Det angivna :attribute har förekommit i ett dataintrång. Vänligen välj ett annat :attribute.',
    ],
    'present' => ':attribute-fältet måste vara närvarande.',
    'present_if' => ':attribute-fältet måste vara närvarande när :other är :value.',
    'present_unless' => ':attribute-fältet måste vara närvarande om inte :other är :value.',
    'present_with' => ':attribute-fältet måste vara närvarande när :values är närvarande.',
    'present_with_all' => ':attribute-fältet måste vara närvarande när :values är närvarande.',
    'prohibited' => ':attribute-fältet är förbjudet.',
    'prohibited_if' => ':attribute-fältet är förbjudet när :other är :value.',
    'prohibited_unless' => ':attribute-fältet är förbjudet om inte :other finns i :values.',
    'prohibits' => ':attribute-fältet förbjuder :other från att vara närvarande.',
    'regex' => ':attribute-fältets format är ogiltigt.',
    'required' => ':attribute-fältet är obligatoriskt.',
    'required_array_keys' => ':attribute-fältet måste innehålla poster för: :values.',
    'required_if' => ':attribute-fältet är obligatoriskt när :other är :value.',
    'required_if_accepted' => ':attribute-fältet är obligatoriskt när :other accepteras.',
    'required_unless' => ':attribute-fältet är obligatoriskt om inte :other finns i :values.',
    'required_with' => ':attribute-fältet är obligatoriskt när :values är närvarande.',
    'required_with_all' => ':attribute-fältet är obligatoriskt när :values är närvarande.',
    'required_without' => ':attribute-fältet är obligatoriskt när :values inte är närvarande.',
    'required_without_all' => ':attribute-fältet är obligatoriskt när ingen av :values är närvarande.',
    'same' => ':attribute-fältet måste matcha :other.',
    'size' => [
        'array' => ':attribute-fältet måste innehålla :size objekt.',
        'file' => ':attribute-fältet måste vara :size kilobyte.',
        'numeric' => ':attribute-fältet måste vara :size.',
        'string' => ':attribute-fältet måste vara :size tecken.',
    ],
    'starts_with' => ':attribute-fältet måste börja med ett av följande: :values.',
    'string' => ':attribute-fältet måste vara en sträng.',
    'timezone' => ':attribute-fältet måste vara en giltig tidszon.',
    'unique' => ':attribute har redan tagits.',
    'uploaded' => ':attribute misslyckades att laddas upp.',
    'uppercase' => ':attribute-fältet måste vara versaler.',
    'url' => ':attribute-fältet måste vara en giltig URL.',
    'ulid' => ':attribute-fältet måste vara en giltig ULID.',
    'uuid' => ':attribute-fältet måste vara ett giltigt UUID.',

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
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
