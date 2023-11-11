<?php

namespace App\Livewire;

use Livewire\Component;
use StudioKaa\Amoclient\Facades\AmoAPI;

const DEBUG_API_RESULT = <<<APIRESULT
[
    {
        "blok": "Blok J",
        "datum_start": "2023-08-01T00:00:00.000000Z",
        "datum_eind": "2024-02-01T00:00:00.000000Z",
        "vakken": [
            {
                "vak": "QUA",
                "volgorde": 0,
                "modules": [
                    {
                        "module": "KYT-II",
                        "leerlijn": "KYT",
                        "week_start": 1,
                        "week_eind": 9,
                        "feedbackmomenten": [
                            {
                                "code": "F4ER",
                                "naam": "Cum dolores.",
                                "week": 2,
                                "points": 2
                            },
                            {
                                "code": "F4ER",
                                "naam": "Cum dolores.",
                                "week": 4,
                                "points": 2
                            },
                            {
                                "code": "F4ER",
                                "naam": "Cum dolores.",
                                "week": 5,
                                "points": 2
                            },
                            {
                                "code": "F4ER",
                                "naam": "Cum dolores.",
                                "week": 7,
                                "points": 2
                            },
                            {
                                "code": "F4ER",
                                "naam": "Cum dolores.",
                                "week": 7,
                                "points": 2
                            },
                            {
                                "code": "F4ER",
                                "naam": "Cum dolores.",
                                "week": 7,
                                "points": 2
                            },
                            {
                                "code": "F4ER",
                                "naam": "Cum dolores.",
                                "week": 12,
                                "points": 2
                            },
                            {
                                "code": "F4ER",
                                "naam": "Cum dolores.",
                                "week": 14,
                                "points": 2
                            }
                        ]
                    },
                    {
                        "module": "SUW-I",
                        "leerlijn": "SUW",
                        "week_start": 10,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FEDS",
                                "naam": "Eveniet odit deserunt.",
                                "week": 4,
                                "points": 8
                            },
                            {
                                "code": "FEDS",
                                "naam": "Eveniet odit deserunt.",
                                "week": 11,
                                "points": 8
                            },
                            {
                                "code": "FEDS",
                                "naam": "Eveniet odit deserunt.",
                                "week": 14,
                                "points": 8
                            },
                            {
                                "code": "FEDS",
                                "naam": "Eveniet odit deserunt.",
                                "week": 16,
                                "points": 8
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "AUT",
                "volgorde": 4,
                "modules": [
                    {
                        "module": "PSW-I",
                        "leerlijn": "PSW",
                        "week_start": 1,
                        "week_eind": 7,
                        "feedbackmomenten": [
                            {
                                "code": "FFGW",
                                "naam": "Non nesciunt vitae consequatur.",
                                "week": 2,
                                "points": 6
                            },
                            {
                                "code": "FFGW",
                                "naam": "Non nesciunt vitae consequatur.",
                                "week": 3,
                                "points": 6
                            },
                            {
                                "code": "FFGW",
                                "naam": "Non nesciunt vitae consequatur.",
                                "week": 8,
                                "points": 6
                            }
                        ]
                    },
                    {
                        "module": "SUW-II",
                        "leerlijn": "SUW",
                        "week_start": 8,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FPRV",
                                "naam": "Ad aut est.",
                                "week": 1,
                                "points": 2
                            },
                            {
                                "code": "FPRV",
                                "naam": "Ad aut est.",
                                "week": 9,
                                "points": 2
                            },
                            {
                                "code": "FPRV",
                                "naam": "Ad aut est.",
                                "week": 13,
                                "points": 2
                            },
                            {
                                "code": "FPRV",
                                "naam": "Ad aut est.",
                                "week": 16,
                                "points": 2
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "EAQ",
                "volgorde": 8,
                "modules": [
                    {
                        "module": "KXC-III",
                        "leerlijn": "KXC",
                        "week_start": 1,
                        "week_eind": 9,
                        "feedbackmomenten": [
                            {
                                "code": "FCVT",
                                "naam": "In aut quo.",
                                "week": 2,
                                "points": 2
                            },
                            {
                                "code": "FCVT",
                                "naam": "In aut quo.",
                                "week": 11,
                                "points": 2
                            }
                        ]
                    },
                    {
                        "module": "IGQ-I",
                        "leerlijn": "IGQ",
                        "week_start": 10,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FEMR",
                                "naam": "Quia voluptate ut amet.",
                                "week": 2,
                                "points": 10
                            },
                            {
                                "code": "FEMR",
                                "naam": "Quia voluptate ut amet.",
                                "week": 7,
                                "points": 10
                            },
                            {
                                "code": "FEMR",
                                "naam": "Quia voluptate ut amet.",
                                "week": 11,
                                "points": 10
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "ET",
                "volgorde": 9,
                "modules": [
                    {
                        "module": "KXC-III",
                        "leerlijn": "KXC",
                        "week_start": 1,
                        "week_eind": 6,
                        "feedbackmomenten": [
                            {
                                "code": "FF9E",
                                "naam": "Qui placeat vel.",
                                "week": 4,
                                "points": 6
                            },
                            {
                                "code": "FF9E",
                                "naam": "Qui placeat vel.",
                                "week": 6,
                                "points": 6
                            },
                            {
                                "code": "FF9E",
                                "naam": "Qui placeat vel.",
                                "week": 6,
                                "points": 6
                            },
                            {
                                "code": "FF9E",
                                "naam": "Qui placeat vel.",
                                "week": 10,
                                "points": 6
                            }
                        ]
                    },
                    {
                        "module": "CZV-IV",
                        "leerlijn": "CZV",
                        "week_start": 7,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FATX",
                                "naam": "Voluptatem dolor neque culpa.",
                                "week": 1,
                                "points": 10
                            },
                            {
                                "code": "FATX",
                                "naam": "Voluptatem dolor neque culpa.",
                                "week": 8,
                                "points": 10
                            },
                            {
                                "code": "FATX",
                                "naam": "Voluptatem dolor neque culpa.",
                                "week": 8,
                                "points": 10
                            },
                            {
                                "code": "FATX",
                                "naam": "Voluptatem dolor neque culpa.",
                                "week": 14,
                                "points": 10
                            },
                            {
                                "code": "FATX",
                                "naam": "Voluptatem dolor neque culpa.",
                                "week": 16,
                                "points": 10
                            }
                        ]
                    }
                ]
            }
        ]
    },
    {
        "blok": "Blok R",
        "datum_start": "2023-08-01T00:00:00.000000Z",
        "datum_eind": "2024-02-01T00:00:00.000000Z",
        "vakken": [
            {
                "vak": "DOL",
                "volgorde": 2,
                "modules": [
                    {
                        "module": "IGQ-III",
                        "leerlijn": "IGQ",
                        "week_start": 1,
                        "week_eind": 11,
                        "feedbackmomenten": [
                            {
                                "code": "FHQN",
                                "naam": "Dolorum est quo.",
                                "week": 2,
                                "points": 4
                            },
                            {
                                "code": "FHQN",
                                "naam": "Dolorum est quo.",
                                "week": 2,
                                "points": 4
                            },
                            {
                                "code": "FHQN",
                                "naam": "Dolorum est quo.",
                                "week": 2,
                                "points": 4
                            }
                        ]
                    },
                    {
                        "module": "SUW-I",
                        "leerlijn": "SUW",
                        "week_start": 12,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FBN7",
                                "naam": "Sint voluptates consequatur dolorem.",
                                "week": 2,
                                "points": 5
                            },
                            {
                                "code": "FBN7",
                                "naam": "Sint voluptates consequatur dolorem.",
                                "week": 9,
                                "points": 5
                            },
                            {
                                "code": "FBN7",
                                "naam": "Sint voluptates consequatur dolorem.",
                                "week": 15,
                                "points": 5
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "NON",
                "volgorde": 3,
                "modules": [
                    {
                        "module": "QPF-II",
                        "leerlijn": "QPF",
                        "week_start": 1,
                        "week_eind": 7,
                        "feedbackmomenten": [
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 3,
                                "points": 6
                            },
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 4,
                                "points": 6
                            },
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 6,
                                "points": 6
                            },
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 9,
                                "points": 6
                            },
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 11,
                                "points": 6
                            },
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 14,
                                "points": 6
                            }
                        ]
                    },
                    {
                        "module": "KXC-II",
                        "leerlijn": "KXC",
                        "week_start": 8,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FFQ3",
                                "naam": "Debitis soluta.",
                                "week": 4,
                                "points": 2
                            },
                            {
                                "code": "FFQ3",
                                "naam": "Debitis soluta.",
                                "week": 11,
                                "points": 2
                            },
                            {
                                "code": "FFQ3",
                                "naam": "Debitis soluta.",
                                "week": 13,
                                "points": 2
                            },
                            {
                                "code": "FFQ3",
                                "naam": "Debitis soluta.",
                                "week": 14,
                                "points": 2
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "IUR",
                "volgorde": 5,
                "modules": [
                    {
                        "module": "EFU-I",
                        "leerlijn": "EFU",
                        "week_start": 1,
                        "week_eind": 7,
                        "feedbackmomenten": [
                            {
                                "code": "FC2S",
                                "naam": "Officiis enim sint.",
                                "week": 3,
                                "points": 2
                            },
                            {
                                "code": "FC2S",
                                "naam": "Officiis enim sint.",
                                "week": 7,
                                "points": 2
                            }
                        ]
                    },
                    {
                        "module": "QPF-II",
                        "leerlijn": "QPF",
                        "week_start": 8,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 3,
                                "points": 6
                            },
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 4,
                                "points": 6
                            },
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 6,
                                "points": 6
                            },
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 9,
                                "points": 6
                            },
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 11,
                                "points": 6
                            },
                            {
                                "code": "FZM9",
                                "naam": "Dolores earum neque.",
                                "week": 14,
                                "points": 6
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "ITA",
                "volgorde": 6,
                "modules": [
                    {
                        "module": "KXC-III",
                        "leerlijn": "KXC",
                        "week_start": 1,
                        "week_eind": 7,
                        "feedbackmomenten": [
                            {
                                "code": "FV4U",
                                "naam": "Nam ut voluptatem vel.",
                                "week": 2,
                                "points": 4
                            },
                            {
                                "code": "FV4U",
                                "naam": "Nam ut voluptatem vel.",
                                "week": 2,
                                "points": 4
                            },
                            {
                                "code": "FV4U",
                                "naam": "Nam ut voluptatem vel.",
                                "week": 4,
                                "points": 4
                            },
                            {
                                "code": "FV4U",
                                "naam": "Nam ut voluptatem vel.",
                                "week": 7,
                                "points": 4
                            },
                            {
                                "code": "FV4U",
                                "naam": "Nam ut voluptatem vel.",
                                "week": 8,
                                "points": 4
                            },
                            {
                                "code": "FV4U",
                                "naam": "Nam ut voluptatem vel.",
                                "week": 8,
                                "points": 4
                            },
                            {
                                "code": "FV4U",
                                "naam": "Nam ut voluptatem vel.",
                                "week": 11,
                                "points": 4
                            }
                        ]
                    },
                    {
                        "module": "CZV-II",
                        "leerlijn": "CZV",
                        "week_start": 8,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "F5UB",
                                "naam": "Nam ut repellendus.",
                                "week": 5,
                                "points": 4
                            },
                            {
                                "code": "F5UB",
                                "naam": "Nam ut repellendus.",
                                "week": 13,
                                "points": 4
                            }
                        ]
                    }
                ]
            }
        ]
    },
    {
        "blok": "Blok Z",
        "datum_start": "2023-08-01T00:00:00.000000Z",
        "datum_eind": "2024-02-01T00:00:00.000000Z",
        "vakken": [
            {
                "vak": "ET",
                "volgorde": 1,
                "modules": [
                    {
                        "module": "PDM-II",
                        "leerlijn": "PDM",
                        "week_start": 1,
                        "week_eind": 7,
                        "feedbackmomenten": [
                            {
                                "code": "FB7H",
                                "naam": "Nihil quos ducimus.",
                                "week": 1,
                                "points": 7
                            },
                            {
                                "code": "FB7H",
                                "naam": "Nihil quos ducimus.",
                                "week": 4,
                                "points": 7
                            },
                            {
                                "code": "FB7H",
                                "naam": "Nihil quos ducimus.",
                                "week": 10,
                                "points": 7
                            },
                            {
                                "code": "FB7H",
                                "naam": "Nihil quos ducimus.",
                                "week": 15,
                                "points": 7
                            }
                        ]
                    },
                    {
                        "module": "TUZ-I",
                        "leerlijn": "TUZ",
                        "week_start": 8,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "F9B3",
                                "naam": "Perspiciatis nihil beatae rerum.",
                                "week": 6,
                                "points": 6
                            },
                            {
                                "code": "F9B3",
                                "naam": "Perspiciatis nihil beatae rerum.",
                                "week": 12,
                                "points": 6
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "DOL",
                "volgorde": 2,
                "modules": [
                    {
                        "module": "IGQ-III",
                        "leerlijn": "IGQ",
                        "week_start": 1,
                        "week_eind": 5,
                        "feedbackmomenten": [
                            {
                                "code": "FG69",
                                "naam": "Sed nobis qui officiis.",
                                "week": 1,
                                "points": 3
                            },
                            {
                                "code": "FG69",
                                "naam": "Sed nobis qui officiis.",
                                "week": 11,
                                "points": 3
                            }
                        ]
                    },
                    {
                        "module": "TSM-I",
                        "leerlijn": "TSM",
                        "week_start": 6,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FVGW",
                                "naam": "Qui rerum aperiam.",
                                "week": 14,
                                "points": 5
                            },
                            {
                                "code": "FVGW",
                                "naam": "Qui rerum aperiam.",
                                "week": 16,
                                "points": 5
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "NON",
                "volgorde": 3,
                "modules": [
                    {
                        "module": "CZV-III",
                        "leerlijn": "CZV",
                        "week_start": 1,
                        "week_eind": 14,
                        "feedbackmomenten": [
                            {
                                "code": "FYZ2",
                                "naam": "Placeat saepe quo qui et.",
                                "week": 2,
                                "points": 5
                            },
                            {
                                "code": "FYZ2",
                                "naam": "Placeat saepe quo qui et.",
                                "week": 4,
                                "points": 5
                            },
                            {
                                "code": "FYZ2",
                                "naam": "Placeat saepe quo qui et.",
                                "week": 9,
                                "points": 5
                            },
                            {
                                "code": "FYZ2",
                                "naam": "Placeat saepe quo qui et.",
                                "week": 15,
                                "points": 5
                            }
                        ]
                    },
                    {
                        "module": "TUZ-III",
                        "leerlijn": "TUZ",
                        "week_start": 15,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FMSU",
                                "naam": "Molestiae omnis architecto harum consequatur.",
                                "week": 4,
                                "points": 1
                            },
                            {
                                "code": "FMSU",
                                "naam": "Molestiae omnis architecto harum consequatur.",
                                "week": 16,
                                "points": 1
                            },
                            {
                                "code": "FMSU",
                                "naam": "Molestiae omnis architecto harum consequatur.",
                                "week": 16,
                                "points": 1
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "MAG",
                "volgorde": 6,
                "modules": [
                    {
                        "module": "EDH-II",
                        "leerlijn": "EDH",
                        "week_start": 1,
                        "week_eind": 12,
                        "feedbackmomenten": [
                            {
                                "code": "FFPX",
                                "naam": "Qui repellendus quasi.",
                                "week": 2,
                                "points": 3
                            },
                            {
                                "code": "FFPX",
                                "naam": "Qui repellendus quasi.",
                                "week": 7,
                                "points": 3
                            },
                            {
                                "code": "FFPX",
                                "naam": "Qui repellendus quasi.",
                                "week": 9,
                                "points": 3
                            },
                            {
                                "code": "FFPX",
                                "naam": "Qui repellendus quasi.",
                                "week": 10,
                                "points": 3
                            }
                        ]
                    },
                    {
                        "module": "SUW-II",
                        "leerlijn": "SUW",
                        "week_start": 13,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FMDX",
                                "naam": "Blanditiis aut sapiente eum.",
                                "week": 3,
                                "points": 4
                            },
                            {
                                "code": "FMDX",
                                "naam": "Blanditiis aut sapiente eum.",
                                "week": 15,
                                "points": 4
                            },
                            {
                                "code": "FMDX",
                                "naam": "Blanditiis aut sapiente eum.",
                                "week": 15,
                                "points": 4
                            },
                            {
                                "code": "FMDX",
                                "naam": "Blanditiis aut sapiente eum.",
                                "week": 16,
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
                "vak": "VEL",
                "volgorde": 0,
                "modules": [
                    {
                        "module": "PDM-I",
                        "leerlijn": "PDM",
                        "week_start": 1,
                        "week_eind": 12,
                        "feedbackmomenten": [
                            {
                                "code": "FN4M",
                                "naam": "Nulla voluptate qui.",
                                "week": 10,
                                "points": 7
                            },
                            {
                                "code": "FN4M",
                                "naam": "Nulla voluptate qui.",
                                "week": 11,
                                "points": 7
                            }
                        ]
                    },
                    {
                        "module": "KXC-I",
                        "leerlijn": "KXC",
                        "week_start": 13,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FN6A",
                                "naam": "Et nulla officia.",
                                "week": 3,
                                "points": 6
                            },
                            {
                                "code": "FN6A",
                                "naam": "Et nulla officia.",
                                "week": 3,
                                "points": 6
                            },
                            {
                                "code": "FN6A",
                                "naam": "Et nulla officia.",
                                "week": 5,
                                "points": 6
                            },
                            {
                                "code": "FN6A",
                                "naam": "Et nulla officia.",
                                "week": 6,
                                "points": 6
                            },
                            {
                                "code": "FN6A",
                                "naam": "Et nulla officia.",
                                "week": 13,
                                "points": 6
                            },
                            {
                                "code": "FN6A",
                                "naam": "Et nulla officia.",
                                "week": 15,
                                "points": 6
                            },
                            {
                                "code": "FN6A",
                                "naam": "Et nulla officia.",
                                "week": 16,
                                "points": 6
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "ET",
                "volgorde": 1,
                "modules": [
                    {
                        "module": "TSM-II",
                        "leerlijn": "TSM",
                        "week_start": 1,
                        "week_eind": 4,
                        "feedbackmomenten": [
                            {
                                "code": "FPWU",
                                "naam": "Laudantium voluptas eaque commodi.",
                                "week": 2,
                                "points": 6
                            },
                            {
                                "code": "FPWU",
                                "naam": "Laudantium voluptas eaque commodi.",
                                "week": 10,
                                "points": 6
                            },
                            {
                                "code": "FPWU",
                                "naam": "Laudantium voluptas eaque commodi.",
                                "week": 12,
                                "points": 6
                            }
                        ]
                    },
                    {
                        "module": "KXC-II",
                        "leerlijn": "KXC",
                        "week_start": 5,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "F5WT",
                                "naam": "Qui quisquam quaerat maxime.",
                                "week": 2,
                                "points": 1
                            },
                            {
                                "code": "F5WT",
                                "naam": "Qui quisquam quaerat maxime.",
                                "week": 4,
                                "points": 1
                            },
                            {
                                "code": "F5WT",
                                "naam": "Qui quisquam quaerat maxime.",
                                "week": 9,
                                "points": 1
                            },
                            {
                                "code": "F5WT",
                                "naam": "Qui quisquam quaerat maxime.",
                                "week": 11,
                                "points": 1
                            },
                            {
                                "code": "F5WT",
                                "naam": "Qui quisquam quaerat maxime.",
                                "week": 11,
                                "points": 1
                            },
                            {
                                "code": "F5WT",
                                "naam": "Qui quisquam quaerat maxime.",
                                "week": 16,
                                "points": 1
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "ILL",
                "volgorde": 2,
                "modules": [
                    {
                        "module": "QPF-I",
                        "leerlijn": "QPF",
                        "week_start": 1,
                        "week_eind": 11,
                        "feedbackmomenten": [
                            {
                                "code": "F4YQ",
                                "naam": "Omnis voluptas temporibus rem.",
                                "week": 7,
                                "points": 7
                            }
                        ]
                    },
                    {
                        "module": "TEV-II",
                        "leerlijn": "TEV",
                        "week_start": 12,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FKY3",
                                "naam": "Maiores aut.",
                                "week": 2,
                                "points": 5
                            },
                            {
                                "code": "FKY3",
                                "naam": "Maiores aut.",
                                "week": 4,
                                "points": 5
                            },
                            {
                                "code": "FKY3",
                                "naam": "Maiores aut.",
                                "week": 4,
                                "points": 5
                            },
                            {
                                "code": "FKY3",
                                "naam": "Maiores aut.",
                                "week": 6,
                                "points": 5
                            },
                            {
                                "code": "FKY3",
                                "naam": "Maiores aut.",
                                "week": 14,
                                "points": 5
                            }
                        ]
                    }
                ]
            },
            {
                "vak": "IUR",
                "volgorde": 5,
                "modules": [
                    {
                        "module": "CZV-III",
                        "leerlijn": "CZV",
                        "week_start": 1,
                        "week_eind": 13,
                        "feedbackmomenten": [
                            {
                                "code": "FFZ3",
                                "naam": "Autem qui laboriosam.",
                                "week": 1,
                                "points": 8
                            },
                            {
                                "code": "FFZ3",
                                "naam": "Autem qui laboriosam.",
                                "week": 9,
                                "points": 8
                            },
                            {
                                "code": "FFZ3",
                                "naam": "Autem qui laboriosam.",
                                "week": 12,
                                "points": 8
                            }
                        ]
                    },
                    {
                        "module": "TUZ-III",
                        "leerlijn": "TUZ",
                        "week_start": 14,
                        "week_eind": 16,
                        "feedbackmomenten": [
                            {
                                "code": "FMSU",
                                "naam": "Molestiae omnis architecto harum consequatur.",
                                "week": 4,
                                "points": 1
                            },
                            {
                                "code": "FMSU",
                                "naam": "Molestiae omnis architecto harum consequatur.",
                                "week": 16,
                                "points": 1
                            },
                            {
                                "code": "FMSU",
                                "naam": "Molestiae omnis architecto harum consequatur.",
                                "week": 16,
                                "points": 1
                            }
                        ]
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
        $data = json_decode(file_get_contents(config('app.currapp.api_url') . '/feedbackmomenten/active-sorted-by-module', false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Bearer ' . config('app.currapp.api_token')
            ]
        ])));
        // $data = json_decode(DEBUG_API_RESULT, false);

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
