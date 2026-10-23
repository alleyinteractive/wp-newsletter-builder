import { act } from 'react';
import { createRoot, Root } from 'react-dom/client';

// Tell React that updates are wrapped in act() so it flushes effects synchronously.
(globalThis as unknown as { IS_REACT_ACT_ENVIRONMENT: boolean }).IS_REACT_ACT_ENVIRONMENT = true;

/**
 * Render an element into a detached container and flush its effects and any
 * resolved promises. Call `unmount()` when the test is done.
 */
export async function render(element: React.ReactElement) {
  const container = document.createElement('div');
  let root: Root;
  await act(async () => {
    root = createRoot(container);
    root.render(element);
  });

  return {
    container,
    rerender: async (next: React.ReactElement) => {
      await act(async () => {
        root.render(next);
      });
    },
    unmount: () => act(() => root.unmount()),
  };
}

/**
 * Let pending promise callbacks and the effects they trigger run.
 */
export async function flush() {
  await act(async () => {
    await new Promise((resolve) => { setTimeout(resolve, 0); });
  });
}

interface SelectControlProps {
  options?: Array<{ label: string; value: string }>;
  value?: string;
}

/**
 * Lightweight stand-ins for @wordpress/components, which needs browser APIs
 * jsdom doesn't provide. Use with
 * `jest.mock('@wordpress/components', () => jest.requireActual('@/test-utils').mockComponents)`.
 */
export const mockComponents = {
  SelectControl: ({ options = [], value }: SelectControlProps) => (
    <select value={value} onChange={() => {}}>
      {options.map((option) => (
        <option key={option.value} value={option.value}>{option.label}</option>
      ))}
    </select>
  ),
  Spinner: () => null,
  TextControl: () => null,
};
