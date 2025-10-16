uniform vec2 u_resolution;
uniform vec2 u_mouse;
uniform float u_time;
uniform sampler2D u_noise;
uniform sampler2D u_buffer;
uniform bool u_renderpass;

const float circleSize = .25;
const float threshold = .5;
const float scale = 4.;

#define _fract true

#define PI 3.141592653589793
#define TAU 6.283185307179586

#define pow2(x) (x * x)

const int samples = 2;
const float sigma = float(samples) * 0.25;

float gaussian(vec2 i) {
	return 1.0 / (2.0 * PI * pow2(sigma)) * exp(-((pow2(i.x) + pow2(i.y)) / (2.0 * pow2(sigma))));
}

vec3 hash33(vec3 p) {
	float n = sin(dot(p, vec3(7, 157, 113)));
	return fract(vec3(2097152, 262144, 32768)*n);
}

void main() {
	vec2 uv = (gl_FragCoord.xy - 0.5 * u_resolution.xy) / u_resolution.y;
	uv *= scale;
	
	vec2 mouse = u_mouse * scale;
	vec2 ps = vec2(1.0) / u_resolution.xy;
	vec2 sample = gl_FragCoord.xy / u_resolution.xy;
	vec2 o = mouse*.2+vec2(.65, .5);
	float d = .98;

	sample = d * (sample - o);
	sample += o;
	sample += vec2(sin((u_time+uv.y * .5)*10.)*.001, -.00);
	vec3 fragcolour;
	vec4 tex;

	if (u_renderpass) {
		tex = texture2D(u_buffer, sample) * 0.8;

		float df = length(mouse - uv);
		fragcolour = vec3(smoothstep(circleSize, 0., df));
	} else {
		tex = texture2D(u_buffer, sample, 2.) * .98;
		tex = vec4(
			smoothstep(0.0, threshold - fwidth(tex.x), tex.x),
			smoothstep(0.2, threshold - fwidth(tex.y) + .2, tex.y),
			smoothstep(-0.05, threshold - fwidth(tex.z) - .2, tex.z),
			1.0
		);
		vec3 n = hash33(vec3(uv,  u_time * .1));
		vec3 circleColor = vec3(
			0.5 + 0.5*sin(u_time),
			0.5 + 0.5*sin(u_time + 2.0),
			0.5 + 0.5*sin(u_time + 4.0)
		);
		fragcolour = circleColor * smoothstep(circleSize, 0., length(mouse - uv));

		tex.rgb += n * .2 - .1;
		tex.rgb = vec3(dot(tex.rgb, vec3(0.299, 0.587, 0.114)));
	}

	gl_FragColor = vec4(fragcolour, 1.0);
	gl_FragColor += tex;
}