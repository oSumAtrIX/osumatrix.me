if (window.innerWidth > 500) {
	let camera, scene, renderer;
	let uniforms;

	let divisor = 1 / 5;

	let loader = new THREE.TextureLoader();
	let texture, rtTexture, rtTexture2;

	let new_mouse = {
		x: 0,
		y: 0
	};

	loader.setCrossOrigin('anonymous');
	loader.load('https://s3-us-west-2.amazonaws.com/s.cdpn.io/982762/noise.png', async (tex) => {
		texture = tex;
		texture.wrapS = THREE.RepeatWrapping;
		texture.wrapT = THREE.RepeatWrapping;
		texture.minFilter = THREE.LinearFilter;

		camera = new THREE.Camera();
		camera.position.z = 1;

		scene = new THREE.Scene();

		const geometry = new THREE.PlaneBufferGeometry(2, 2);

		rtTexture = new THREE.WebGLRenderTarget(window.innerWidth * 0.2, window.innerHeight * 0.2);
		rtTexture2 = new THREE.WebGLRenderTarget(window.innerWidth * 0.2, window.innerHeight * 0.2);

		uniforms = {
			u_time: {
				type: 'f',
				value: 1.0
			},
			u_resolution: {
				type: 'v2',
				value: new THREE.Vector2()
			},
			u_noise: {
				type: 't',
				value: texture
			},
			u_buffer: {
				type: 't',
				value: rtTexture.texture
			},
			u_mouse: {
				type: 'v2',
				value: new THREE.Vector2()
			},
			u_renderpass: {
				type: 'b',
				value: false
			}
		};

		const material = new THREE.ShaderMaterial({
			uniforms: uniforms,
			vertexShader: await (await fetch('cursor_trail_vertex.glsl')).text(),
			fragmentShader: await (await fetch('cursor_trail_fragment.glsl')).text()
		});

		material.extensions.derivatives = true;

		const mesh = new THREE.Mesh(geometry, material);
		scene.add(mesh);

		renderer = new THREE.WebGLRenderer();
		renderer.setPixelRatio(window.devicePixelRatio);

		document.body.prepend(renderer.domElement);

		onWindowResize();
		window.addEventListener('resize', onWindowResize, false);
		window.addEventListener('orientationchange', onWindowResize, false);

		document.addEventListener('pointermove', (e) => {
			let ratio = window.innerHeight / window.innerWidth;
			new_mouse.x = (e.pageX - window.innerWidth / 2) / window.innerWidth / ratio;
			new_mouse.y = ((e.pageY - window.innerHeight / 2) / window.innerHeight) * -1;

			e.preventDefault();
		});

		animate();
	});

	function onWindowResize() {
		renderer.setSize(window.innerWidth, window.innerHeight);
		uniforms.u_resolution.value.x = renderer.domElement.width;
		uniforms.u_resolution.value.y = renderer.domElement.height;

		rtTexture = new THREE.WebGLRenderTarget(window.innerWidth * 0.2, window.innerHeight * 0.2);
		rtTexture2 = new THREE.WebGLRenderTarget(window.innerWidth * 0.2, window.innerHeight * 0.2);
	}

	function animate(delta) {
		setTimeout(() => {
			requestAnimationFrame(animate);
		}, 1000 / 144);
		uniforms.u_mouse.value.x += (new_mouse.x - uniforms.u_mouse.value.x) * divisor;
		uniforms.u_mouse.value.y += (new_mouse.y - uniforms.u_mouse.value.y) * divisor;

		uniforms.u_time.value = delta * 0.0005;
		renderer.render(scene, camera);

		let odims = uniforms.u_resolution.value.clone();
		uniforms.u_resolution.value.x = window.innerWidth * 0.2;
		uniforms.u_resolution.value.y = window.innerHeight * 0.2;

		uniforms.u_buffer.value = rtTexture2.texture;

		uniforms.u_renderpass.value = true;

		window.rtTexture = rtTexture;
		renderer.setRenderTarget(rtTexture);
		renderer.render(scene, camera, rtTexture, true);

		let buffer = rtTexture;
		rtTexture = rtTexture2;
		rtTexture2 = buffer;

		uniforms.u_buffer.value = rtTexture.texture;
		uniforms.u_resolution.value = odims;
		uniforms.u_renderpass.value = false;
	}
}
