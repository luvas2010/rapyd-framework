#note sviluppo

## usare meglio i model ##

in crud complicati, o in datagrid con colonne da formattare, rapyd prevede le callback (nota: rinominerei per conformità in "callback" anche le pre/postprocess\_functions del dataedit)

Attualmente, l'uso prevede il richiamo a "funzioni" o a metodi del controller.
Sarebbe bene mostrare negli esempi che è piu' giusto chiamare metodi di un "model".

(per il datagrid come si fà? visto che per default non è associato ad un model?, sarebbe giusto eventualmente prevedere un model per operare con i filtri e con i dataset?
per intenderci: spostare il "dataset" nel folder model? ...)


## da $datagrid->db  a $datagrid->model->db ##

una soluzione "semplice" potrebbe essere quella di spostare l'accesso e l'uso del db dai componenti ai model ("dataset" o "datamodel").

In maniera tale che, quando serve fare callback, si possano estendere i model per mettere lì (e non nel controller o in funzioni sparse) le operazioni sui dati.

in generale tutto cio' che è $componente->db  dovrebbe diventare $componente->model->db o meglio $componente->model->metodo\_che\_opera\_su\_db esempio..
questo puo' forse essere fatto a livello di "component".. (classe ancestor di tutti i componenti ma và verificato)


## todo ##

  * Integrare le ultime modifiche alle pre\_post process function e approfittare per rinominare in callback
  * provare a spostare il dataset nel folder model
  * provare a estendere i model negli esempi e spostare lì la parte che opera sul db


```
test
.
└── default
    └── base
        ├── css 
        ├── images
        │   ├── banks
        │   ├── dashboard
        │   ├── default
        │   ├── flags
        │   ├── menu
        │   └── stars
        ├── js
        └── scripts
```