import { HeroScene } from "../../components/3d/HeroScene";
import { Link } from "react-router-dom";

export function Test3DPage() {
  return (
    <div className="relative min-h-screen w-full overflow-hidden bg-slate-50 flex flex-col items-center justify-center">
      {/* مشهد الـ 3D يعمل كخلفية (Background) */}
      <HeroScene />

      {/* المحتوى (Text & Buttons) يظهر فوق الـ 3D */}
      <div className="relative z-10 text-center px-4 max-w-3xl pointer-events-auto">
        <h1 className="text-5xl md:text-7xl font-bold text-slate-900 mb-6 drop-shadow-sm">
          تجربة الواجهة ثلاثية الأبعاد
        </h1>
        <p className="text-xl text-slate-700 mb-10 leading-relaxed max-w-2xl mx-auto drop-shadow-sm">
          هذه الصفحة مخصصة لاختبار المكونات الـ 3D (الأشكال العائمة والإضاءة) 
          قبل اعتمادها في الصفحة الرئيسية لمشروع "معك". حرك الماوس لرؤية التفاعل!
        </p>
        
        <div className="flex flex-wrap justify-center gap-4">
          <button className="px-8 py-3 rounded-md text-lg font-medium bg-[#1E6091] hover:bg-[#184e77] text-white transition-colors">
            زر تفاعلي 1
          </button>
          <button className="px-8 py-3 rounded-md text-lg font-medium bg-white/80 backdrop-blur-sm border border-[#52B788] text-[#1E6091] hover:bg-[#52B788] hover:text-white transition-colors">
            زر تفاعلي 2
          </button>
          <Link to="/">
            <button className="px-8 py-3 rounded-md text-lg font-medium bg-transparent text-slate-600 hover:text-slate-900 transition-colors">
              العودة للرئيسية
            </button>
          </Link>
        </div>
      </div>
    </div>
  );
}
