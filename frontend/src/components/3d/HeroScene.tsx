import { Canvas } from '@react-three/fiber';
import { Environment, PerspectiveCamera, ContactShadows } from '@react-three/drei';
import { GlassCards } from './GlassCards';
import { Suspense } from 'react';

export function HeroScene() {
  return (
    // هذا الحاوي (Container) سيكون ثابتًا كخلفية (Background)
    <div className="absolute inset-0 z-0" style={{ height: '100%', width: '100%' }}>
      {/* Canvas هو المكان الذي ترسم فيه مكتبة Three.js */}
      <Canvas dpr={[1, 2]}>
        <PerspectiveCamera makeDefault position={[0, 0, 6]} fov={50} />
        
        {/* الإضاءة */}
        <ambientLight intensity={0.8} />
        <directionalLight position={[10, 10, 10]} intensity={1.5} />
        <directionalLight position={[-10, -10, -10]} intensity={0.5} color="#52B788" />
        
        {/* Suspense يمنع تعليق الشاشة بينما يتم تحميل بيئة הـ 3D */}
        <Suspense fallback={null}>
          <GlassCards />
          {/* بيئة إضاءة واقعية تنعكس على الأشكال والزجاج */}
          <Environment preset="city" />
          {/* ظل خفيف تحت الأشكال ليعطي إحساساً بالعمق */}
          <ContactShadows position={[0, -3, 0]} opacity={0.5} scale={20} blur={2} far={4} color="#1E6091" />
        </Suspense>
      </Canvas>
    </div>
  );
}
