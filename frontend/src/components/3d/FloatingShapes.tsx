import { useRef } from 'react';
import { useFrame } from '@react-three/fiber';
import { Float, MeshDistortMaterial } from '@react-three/drei';
import * as THREE from 'three';

export function FloatingShapes() {
  const group = useRef<THREE.Group>(null);

  // نستخدم useFrame لتحريك المجموعة كاملة بشكل ناعم بناءً على حركة الماوس
  useFrame((state) => {
    if (!group.current) return;
    group.current.rotation.x = THREE.MathUtils.lerp(group.current.rotation.x, (state.pointer.y * Math.PI) / 4, 0.05);
    group.current.rotation.y = THREE.MathUtils.lerp(group.current.rotation.y, (state.pointer.x * Math.PI) / 4, 0.05);
  });

  return (
    <group ref={group}>
      {/* الشكل الأول: حلقة ملتوية (TorusKnot) بلون الأخضر المميز للمشروع */}
      <Float speed={2} rotationIntensity={1} floatIntensity={1.5} position={[2, 1, -2]}>
        <mesh>
          <torusKnotGeometry args={[0.8, 0.3, 100, 16]} />
          <MeshDistortMaterial color="#52B788" distort={0.4} speed={2} roughness={0.5} />
        </mesh>
      </Float>

      {/* الشكل الثاني: كرة سائلة (Sphere) بلون أزرق داكن */}
      <Float speed={1.5} rotationIntensity={1.5} floatIntensity={2} position={[-2, -1, 1]}>
        <mesh>
          <sphereGeometry args={[1, 64, 64]} />
          <MeshDistortMaterial color="#1E6091" distort={0.6} speed={3} roughness={0.2} metalness={0.8} />
        </mesh>
      </Float>

      {/* الشكل الثالث: شكل هندسي ماسي (Octahedron) بلون رمادي فاتح */}
      <Float speed={2.5} rotationIntensity={0.5} floatIntensity={1} position={[1, -2, -1]}>
        <mesh>
          <octahedronGeometry args={[0.6, 0]} />
          <MeshDistortMaterial color="#E0E1DD" distort={0.2} speed={1.5} roughness={0.7} metalness={0.1} />
        </mesh>
      </Float>
    </group>
  );
}
