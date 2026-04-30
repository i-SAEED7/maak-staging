import { useRef, useState } from 'react';
import { useFrame } from '@react-three/fiber';
import { Float, MeshTransmissionMaterial, Text } from '@react-three/drei';
import * as THREE from 'three';
import { useSpring, a } from '@react-spring/three';

export function GlassCards() {
  const group = useRef<THREE.Group>(null);

  // تحريك المجموعة كاملة بشكل ناعم جداً بناءً على حركة الماوس
  useFrame((state) => {
    if (!group.current) return;
    // حركة خفيفة للماوس (Parallax effect)
    group.current.rotation.y = THREE.MathUtils.lerp(group.current.rotation.y, (state.pointer.x * Math.PI) / 8, 0.05);
    group.current.rotation.x = THREE.MathUtils.lerp(group.current.rotation.x, -(state.pointer.y * Math.PI) / 16, 0.05);
  });

  return (
    <group ref={group} position={[0, 0, 0]}>
      {/* العناصر الملونة في الخلفية (التي ستظهر خلف الزجاج) */}
      <Float speed={2} rotationIntensity={1} floatIntensity={2} position={[-2.5, 1.5, -2]}>
        <mesh>
          <sphereGeometry args={[1, 64, 64]} />
          <meshPhysicalMaterial color="#52B788" roughness={0.1} metalness={0.8} />
        </mesh>
      </Float>

      <Float speed={1.5} rotationIntensity={2} floatIntensity={1.5} position={[2.5, -1.5, -3]}>
        <mesh rotation={[Math.PI / 4, Math.PI / 4, 0]}>
          <torusGeometry args={[1, 0.4, 64, 100]} />
          <meshPhysicalMaterial color="#1E6091" roughness={0.2} metalness={0.9} />
        </mesh>
      </Float>

      <Float speed={3} rotationIntensity={0.5} floatIntensity={1} position={[0, 0, -4]}>
         <mesh>
          <icosahedronGeometry args={[1.5, 0]} />
          <meshPhysicalMaterial color="#FCA311" roughness={0.3} metalness={0.6} />
        </mesh>
      </Float>


      {/* البطاقة الزجاجية الرئيسية (التي تعطي تأثير Apple Glassmorphism) */}
      <GlassCard position={[0, 0, 1]} rotation={[0, 0, 0]} title="البوابة المتكاملة" subtitle="لقسم ذوي الإعاقة" />
      
      {/* بطاقات زجاجية جانبية أصغر */}
      <GlassCard position={[-3, -1, 0]} rotation={[0, Math.PI / 8, 0]} scale={0.7} title="خطط فردية" subtitle="ذكية" />
      <GlassCard position={[3, 1, -0.5]} rotation={[0, -Math.PI / 8, 0]} scale={0.6} title="تقارير" subtitle="متقدمة" />

    </group>
  );
}

// مكون البطاقة الزجاجية الواحدة
function GlassCard({ position, rotation, scale = 1, title, subtitle }: any) {
  const meshRef = useRef<THREE.Mesh>(null);
  const [hovered, setHovered] = useState(false);

  // استخدام react-spring لجعل التفاعل (Hover) سلساً وممتازاً
  const { scale: animatedScale } = useSpring({
    scale: hovered ? scale * 1.05 : scale,
    config: { mass: 1, tension: 280, friction: 60 }
  });

  return (
    <a.group position={position} rotation={rotation} scale={animatedScale as any}>
      <mesh 
        ref={meshRef} 
        onPointerOver={() => setHovered(true)} 
        onPointerOut={() => setHovered(false)}
      >
        <boxGeometry args={[4, 2.5, 0.1]} />
        
        {/* المادة الزجاجية المتقدمة (MeshTransmissionMaterial) */}
        <MeshTransmissionMaterial
          backside
          samples={4}
          thickness={0.5}
          chromaticAberration={0.025}
          anisotropy={0.1}
          distortion={0.1}
          distortionScale={0.5}
          temporalDistortion={0.0}
          iridescence={1}
          iridescenceIOR={1}
          iridescenceThicknessRange={[0, 1400]}
          clearcoat={1}
          clearcoatRoughness={0}
          transmission={1}
          opacity={1}
          roughness={0.1}
          metalness={0.1}
        />
      </mesh>

      {/* النصوص المنقوشة على الزجاج */}
      <Text
        position={[0, 0.3, 0.1]}
        fontSize={0.4}
        color="#1e293b" // لون داكن للنص
        anchorX="center"
        anchorY="middle"
      >
        {title}
      </Text>
      
      <Text
        position={[0, -0.3, 0.1]}
        fontSize={0.2}
        color="#64748b" // لون رمادي للنص الفرعي
        anchorX="center"
        anchorY="middle"
      >
        {subtitle}
      </Text>
    </a.group>
  );
}
