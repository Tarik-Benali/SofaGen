@extends('main')

@section('content')

  <div class="sidebar">
    <div class="widget" draggable="true" data-object="box">Boîte</div>
    <div class="widget" draggable="true" data-object="sphere">Sphère</div>
    <div class="widget" draggable="true" data-object="cylinder">Cylindre</div>
    <!-- Ajoutez d'autres éléments de widget ici -->
  </div>

  <canvas style="width: 100%; margin-left: 30px;" id="renderCanvas" touch-action="none"></canvas>

  <script src="https://cdn.babylonjs.com/babylon.js"></script>
  <script src="https://cdn.babylonjs.com/loaders/babylonjs.loaders.min.js"></script>
  <script src="https://code.jquery.com/pep/0.4.3/pep.js"></script>

  <script>
    const canvas = document.getElementById("renderCanvas");
    const engine = new BABYLON.Engine(canvas, true);

    const createScene = function () {
      const scene = new BABYLON.Scene(engine);

      // Importer le mesh de la boîte
      // BABYLON.SceneLoader.ImportMeshAsync("", "https://assets.babylonjs.com/meshes/", "box.babylon");

      // Create the box
      const box = BABYLON.MeshBuilder.CreateBox("box", { height: 1, width: 1, depth: 1 }, scene);
      box.position.x = -6.8;
      box.position.y = 0;
      box.position.z = 0;
      box.rotation.y = Math.PI / 2;

      // Create the ground
      const ground = BABYLON.MeshBuilder.CreateGround("ground", { width: 15, height: 10 }, scene);
      ground.position.y = -0.5;
      ground.position.z = 0;
      ground.material = new BABYLON.StandardMaterial("groundMat", scene);
      ground.material.diffuseColor = new BABYLON.Color3(0.6, 0.6, 0.7); // set the ground color to gray

      // Create the wall
      const wall = BABYLON.MeshBuilder.CreateBox("wall", { height: 6, width: 10, depth: 0.1 }, scene);
      wall.rotation.y = Math.PI / 2;
      wall.position.y = 2.5;
      wall.position.x = -7.5;
      wall.position.z = 0;

      wall.material = new BABYLON.StandardMaterial("wallMat", scene);
      wall.material.diffuseColor = new BABYLON.Color3(0.5, 0.5, 0.5); // set the wall color to gray

      // Create the left wall
      const leftWall = BABYLON.MeshBuilder.CreateBox("leftWall", { height: 6, width: 0.1, depth: 15 }, scene);
      leftWall.rotation.y = Math.PI / 2;
      leftWall.position.y = 2.5;
      leftWall.position.z = 5;
      leftWall.material = new BABYLON.StandardMaterial("wallMat", scene);
      leftWall.material.diffuseColor = new BABYLON.Color3(0.5, 0.5, 0.5); // set the wall color to gray

      // Create the right wall
      const rightWall = BABYLON.MeshBuilder.CreateBox("rightWall", { height: 6, width: 0.1, depth: 15 }, scene);
      rightWall.rotation.y = Math.PI / 2;
      rightWall.position.y = 2.5;
      rightWall.position.z = -5;
      rightWall.material = new BABYLON.StandardMaterial("wallMat", scene);
      rightWall.material.diffuseColor = new BABYLON.Color3(0.5, 0.5, 0.5); // set the wall color to gray

      // Create the ceiling
      const ceiling = BABYLON.MeshBuilder.CreatePlane("ceiling", { width: 15, height: 10 }, scene);
      ceiling.rotation.x = -Math.PI / 2;
      ceiling.position.y = 5.5;
      ceiling.position.z = 0;
      ceiling.material = new BABYLON.StandardMaterial("ceilingMat", scene);
      ceiling.material.diffuseColor = new BABYLON.Color3(0.5, 0.5, 0.5); // set the ceiling color to gray

      const camera = new BABYLON.ArcRotateCamera("camera", -6.3, Math.PI / 2.2, 10, new BABYLON.Vector3(0, 0, 0));
      camera.attachControl(canvas, true);
      const light = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(1, 1, 0));

      return scene;
    };

    const scene = createScene();

    engine.runRenderLoop(function () {
      scene.render();
    });

    window.addEventListener("resize", function () {
      engine.resize();
    });

    // Fonction de gestion de l'événement dragstart pour initialiser le glisser-déposer
    function handleDragStart(event) {
      event.dataTransfer.setData('text/plain', event.target.dataset.object);
    }

    // Fonction de gestion de l'événement dragover pour autoriser le glisser-déposer
    function handleDragOver(event) {
      event.preventDefault();
    }

    // Fonction de gestion de l'événement drop pour ajouter des objets 3D dans l'espace de travail
    function handleDrop(event) {
      event.preventDefault();

      // Récupérez l'ID de l'objet 3D depuis les données de transfert
      const objectId = event.dataTransfer.getData('text/plain');

      // Créez l'objet 3D correspondant en fonction de l'ID
      const object = createObject(objectId);

      // Positionnez l'objet 3D à côté de l'objet existant
      const offsetX = 1.5; // Offset en X par rapport à l'objet existant
      const existingObject = scene.getMeshByName('box'); // Remplacez 'box' par le nom de votre objet existant
      if (existingObject) {
        object.position.x = existingObject.position.x + offsetX;
      }
    }

    // Créez l'objet 3D en fonction de l'ID
    function createObject(objectId) {
      let object;

      switch (objectId) {
        case 'box':
          object = BABYLON.MeshBuilder.CreateBox("box", { height: 1, width: 1, depth: 1 }, scene);
          break;
        case 'sphere':
          object = BABYLON.MeshBuilder.CreateSphere("sphere", { diameter: 1 }, scene);
          break;
        case 'cylinder':
          object = BABYLON.MeshBuilder.CreateCylinder("cylinder", { diameterTop: 1, diameterBottom: 1, height: 1 }, scene);
          break;
        // Ajoutez d'autres cas pour les autres types d'objets
        default:
          break;
      }

      if (object) {
        object.position.y = 0;
        object.position.z = 0;
        object.rotation.y = Math.PI / 2;
      }

      return object;
    }

    // Récupérez les widgets
    const widgets = document.querySelectorAll('.widget');

    // Ajoutez les gestionnaires d'événements de glisser-déposer aux widgets
    widgets.forEach(function (widget) {
      widget.addEventListener('dragstart', handleDragStart);
    });

    // Ajoutez le gestionnaire d'événement de glisser-déposer à l'espace de travail
    canvas.addEventListener('dragover', handleDragOver);
    canvas.addEventListener('drop', handleDrop);
  </script>

@endsection