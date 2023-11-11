<?php

namespace App\Livewire;

use Livewire\Component;
use StudioKaa\Amoclient\Facades\AmoAPI;

const DEBUG_API_RESULT = <<<APIRESULT
[
    {
        "blok": "Blok O",
        "datum_start": "2023-08-01T00:00:00.000000Z",
        "datum_eind": "2024-02-01T00:00:00.000000Z",
        "vakken": [
            {
                "vak": "QUI",
                "volgorde": 0,
                "modules": [
                    {
                        "module": "YHT-I",
                        "leerlijn": "YHT",
                        "week_start": 1,
                        "week_eind": 11,
                        "feedbackmomenten": [
                            {
                                "code": "FVQ4",
                                "naam": "Hic et.",
                                "week": 4,
                                "points": 1
                            }
                        ]
                    },
                    {
                        "module": "QER-II",
                        "leerlijn": "QER",
                        "week_start": 12,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "F79A",
                                "naam": "Ab quo consequatur.",
                                "week": 1,
                                "points": 5
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "MOL",
                "volgorde": 5,
                "modules": [
                    {
                        "module": "QER-II",
                        "leerlijn": "QER",
                        "week_start": 1,
                        "week_eind": 13,
                        "feedbackmomenten": [
                            {
                                "code": "F79A",
                                "naam": "Ab quo consequatur.",
                                "week": 1,
                                "points": 5
                            }
                        ]
                    },
                    {
                        "module": "FKD-II",
                        "leerlijn": "FKD",
                        "week_start": 14,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FJSM",
                                "naam": "Voluptatibus sed rerum qui.",
                                "week": 2,
                                "points": 5
                            },
                            {
                                "code": "FTVW",
                                "naam": "Quis aliquam iusto.",
                                "week": 15,
                                "points": 2
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "MIN",
                "volgorde": 7,
                "modules": [
                    {
                        "module": "VON-II",
                        "leerlijn": "VON",
                        "week_start": 1,
                        "week_eind": 14,
                        "feedbackmomenten": []
                    },
                    {
                        "module": "WCI-II",
                        "leerlijn": "WCI",
                        "week_start": 15,
                        "week_eind": 16,
                        "feedbackmomenten": {
                            "1": {
                                "code": "FQTV",
                                "naam": "Libero nesciunt ipsum.",
                                "week": 2,
                                "points": 6
                            },
                            "0": {
                                "code": "FBDP",
                                "naam": "Eligendi et id esse.",
                                "week": 13,
                                "points": 9
                            }
                        }
                    }
                ]
            },
            {
                "vak": "ILL",
                "volgorde": 9,
                "modules": [
                    {
                        "module": "JEM-I",
                        "leerlijn": "JEM",
                        "week_start": 1,
                        "week_eind": 10,
                        "feedbackmomenten": []
                    },
                    {
                        "module": "FKD-II",
                        "leerlijn": "FKD",
                        "week_start": 11,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "F3QM",
                                "naam": "Animi dignissimos commodi optio.",
                                "week": 15,
                                "points": 10
                            },
                            {
                                "code": "FRJ4",
                                "naam": "Nisi expedita ipsam id.",
                                "week": 15,
                                "points": 4
                            }
                        ]
                    }
                ]
            }
        ]
    },
    {
        "blok": "Blok H",
        "datum_start": "2023-08-01T00:00:00.000000Z",
        "datum_eind": "2024-02-01T00:00:00.000000Z",
        "vakken": [
            {
                "vak": "COR",
                "volgorde": 4,
                "modules": [
                    {
                        "module": "YHT-II",
                        "leerlijn": "YHT",
                        "week_start": 1,
                        "week_eind": 9,
                        "feedbackmomenten": [
                            {
                                "code": "FY5T",
                                "naam": "Repudiandae nihil adipisci ad repellendus.",
                                "week": 7,
                                "points": 1
                            }
                        ]
                    },
                    {
                        "module": "QER-III",
                        "leerlijn": "QER",
                        "week_start": 10,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FAGS",
                                "naam": "Beatae ipsa ea.",
                                "week": 7,
                                "points": 3
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "ENI",
                "volgorde": 6,
                "modules": [
                    {
                        "module": "VON-II",
                        "leerlijn": "VON",
                        "week_start": 1,
                        "week_eind": 8,
                        "feedbackmomenten": [
                            {
                                "code": "FH5P",
                                "naam": "Aut inventore ut voluptatem.",
                                "week": 12,
                                "points": 3
                            }
                        ]
                    },
                    {
                        "module": "DHH-I",
                        "leerlijn": "DHH",
                        "week_start": 9,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FJMP",
                                "naam": "Modi tenetur dolorem quia.",
                                "week": 10,
                                "points": 6
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "EST",
                "volgorde": 7,
                "modules": [
                    {
                        "module": "UPF-II",
                        "leerlijn": "UPF",
                        "week_start": 1,
                        "week_eind": 3,
                        "feedbackmomenten": []
                    },
                    {
                        "module": "VON-I",
                        "leerlijn": "VON",
                        "week_start": 4,
                        "week_eind": 16,
                        "feedbackmomenten": []
                    }
                ]
            },
            {
                "vak": "ASS",
                "volgorde": 9,
                "modules": [
                    {
                        "module": "KCN-I",
                        "leerlijn": "KCN",
                        "week_start": 1,
                        "week_eind": 14,
                        "feedbackmomenten": []
                    },
                    {
                        "module": "KCN-III",
                        "leerlijn": "KCN",
                        "week_start": 15,
                        "week_eind": 16,
                        "feedbackmomenten": []
                    }
                ]
            }
        ]
    },
    {
        "blok": "Blok A",
        "datum_start": "2023-08-01T00:00:00.000000Z",
        "datum_eind": "2024-02-01T00:00:00.000000Z",
        "vakken": [
            {
                "vak": "NEC",
                "volgorde": 0,
                "modules": [
                    {
                        "module": "FKD-II",
                        "leerlijn": "FKD",
                        "week_start": 1,
                        "week_eind": 8,
                        "feedbackmomenten": [
                            {
                                "code": "F3QM",
                                "naam": "Animi dignissimos commodi optio.",
                                "week": 15,
                                "points": 10
                            },
                            {
                                "code": "FRJ4",
                                "naam": "Nisi expedita ipsam id.",
                                "week": 15,
                                "points": 4
                            }
                        ]
                    },
                    {
                        "module": "DUU-I",
                        "leerlijn": "DUU",
                        "week_start": 9,
                        "week_eind": 16,
                        "feedbackmomenten": []
                    }
                ]
            },
            {
                "vak": "IPS",
                "volgorde": 1,
                "modules": [
                    {
                        "module": "XST-II",
                        "leerlijn": "XST",
                        "week_start": 1,
                        "week_eind": 6,
                        "feedbackmomenten": [
                            {
                                "code": "F5UB",
                                "naam": "Et et saepe minus aliquid.",
                                "week": 6,
                                "points": 9
                            }
                        ]
                    },
                    {
                        "module": "QAY-I",
                        "leerlijn": "QAY",
                        "week_start": 7,
                        "week_eind": 16,
                        "feedbackmomenten": []
                    }
                ]
            },
            {
                "vak": "COR",
                "volgorde": 4,
                "modules": [
                    {
                        "module": "KCN-III",
                        "leerlijn": "KCN",
                        "week_start": 1,
                        "week_eind": 9,
                        "feedbackmomenten": []
                    },
                    {
                        "module": "HVP-II",
                        "leerlijn": "HVP",
                        "week_start": 10,
                        "week_eind": 16,
                        "feedbackmomenten": []
                    }
                ]
            },
            {
                "vak": "MIN",
                "volgorde": 7,
                "modules": [
                    {
                        "module": "WCI-III",
                        "leerlijn": "WCI",
                        "week_start": 1,
                        "week_eind": 11,
                        "feedbackmomenten": [
                            {
                                "code": "FXN2",
                                "naam": "Assumenda odit officia.",
                                "week": 15,
                                "points": 2
                            }
                        ]
                    },
                    {
                        "module": "QAY-I",
                        "leerlijn": "QAY",
                        "week_start": 12,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FEV9",
                                "naam": "Et consectetur ipsam accusamus.",
                                "week": 10,
                                "points": 6
                            }
                        ]
                    }
                ]
            }
        ]
    },
    {
        "blok": "Blok P",
        "datum_start": "2023-08-01T00:00:00.000000Z",
        "datum_eind": "2024-02-01T00:00:00.000000Z",
        "vakken": [
            {
                "vak": "IPS",
                "volgorde": 1,
                "modules": [
                    {
                        "module": "DUU-II",
                        "leerlijn": "DUU",
                        "week_start": 1,
                        "week_eind": 6,
                        "feedbackmomenten": [
                            {
                                "code": "FRT3",
                                "naam": "Inventore ab est temporibus nihil.",
                                "week": 8,
                                "points": 10
                            }
                        ]
                    },
                    {
                        "module": "YHT-I",
                        "leerlijn": "YHT",
                        "week_start": 7,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FVQ4",
                                "naam": "Hic et.",
                                "week": 4,
                                "points": 1
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "HIC",
                "volgorde": 4,
                "modules": [
                    {
                        "module": "WCI-II",
                        "leerlijn": "WCI",
                        "week_start": 1,
                        "week_eind": 5,
                        "feedbackmomenten": {
                            "1": {
                                "code": "FQTV",
                                "naam": "Libero nesciunt ipsum.",
                                "week": 2,
                                "points": 6
                            },
                            "0": {
                                "code": "FBDP",
                                "naam": "Eligendi et id esse.",
                                "week": 13,
                                "points": 9
                            }
                        }
                    },
                    {
                        "module": "HVP-III",
                        "leerlijn": "HVP",
                        "week_start": 6,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FGDR",
                                "naam": "Quia minus dolorem recusandae.",
                                "week": 9,
                                "points": 6
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "MOL",
                "volgorde": 5,
                "modules": [
                    {
                        "module": "UPF-II",
                        "leerlijn": "UPF",
                        "week_start": 1,
                        "week_eind": 4,
                        "feedbackmomenten": []
                    },
                    {
                        "module": "PJU-I",
                        "leerlijn": "PJU",
                        "week_start": 5,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FTCE",
                                "naam": "Et sunt quia ullam.",
                                "week": 8,
                                "points": 8
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "NEC",
                "volgorde": 8,
                "modules": [
                    {
                        "module": "JEM-I",
                        "leerlijn": "JEM",
                        "week_start": 1,
                        "week_eind": 8,
                        "feedbackmomenten": []
                    },
                    {
                        "module": "KCN-I",
                        "leerlijn": "KCN",
                        "week_start": 9,
                        "week_eind": 16,
                        "feedbackmomenten": []
                    }
                ]
            }
        ]
    }
]
APIRESULT;

class StudyPointMatrix extends Component
{
    public $matrix;
    public $students;

    public function render()
    {
        return view('livewire.study-point-matrix');
    }

    public function mount()
    {
        // fetch config('app.currapp.api_url') with header bearer config('app.currapp.api_token') and store in $data
        // $data = json_decode(file_get_contents(config('app.currapp.api_url') . '/feedbackmomenten/active-sorted-by-module', false, stream_context_create([
        //     'http' => [
        //         'method' => 'GET',
        //         'header' => 'Authorization: Bearer ' . config('app.currapp.api_token')
        //     ]
        // ])));
        $data = json_decode(DEBUG_API_RESULT, false);

        // For testing just get the first blok:
        $matrix = $data[0];

        // TODO: clean up spaghetti code
        $matrix->totalFeedbackmomenten = collect($matrix->vakken)->pluck('modules')->map(fn($v) => collect($v)->pluck('feedbackmomenten')->map(fn($v) => collect($v)->flatten()->toArray())->flatten()->toArray())->flatten()->count();

        $group = AmoAPI::get('groups/find/TTSDB-sd4o22e');

        $students = collect($group['users'])
            ->map(function($user) use ($group) {
                return (object) [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'group' => $group['name'],
                    'feedbackmomenten' => [
                    ]
                ];
            });

        $this->students = $students;
        $this->matrix = $matrix;
    }
}
